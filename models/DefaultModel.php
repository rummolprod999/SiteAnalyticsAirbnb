<?php

require_once 'Model.php';

class DefaultModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->create_connection();
    }

    public function get_data()
    {
        $add_mess = $this->add_url();
        $rem_mess = $this->remove_url();
        $launch_mess = $this->launch_parser();
        $clean_changes = $this->bookable_seen();
        $data = $this->get_list_url();
        $url = $this->get_URL(1);
        $ind = count ($data['url_arr']) - 1;
        $data['url_arr'][$ind][1] = $url;
        $change_notes = $this->change_notes();
        $reorders = $this->reorder_table();
        if ($launch_mess !== '') {
            $_SESSION['launch_mess'] = $launch_mess;
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }
        if ($clean_changes !== '') {
            $_SESSION['clean_changes'] = $clean_changes;
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }
        if ($change_notes !== '') {
            $_SESSION['change_notes'] = $change_notes;
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }
        if ($add_mess !== '') {
            $_SESSION['add_mess'] = $add_mess;
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }
        if ($rem_mess !== '') {
            $_SESSION['rem_mess'] = $rem_mess;
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }
        if ($reorders !== '') {
            $_SESSION['reorder'] = $reorders;
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }

        return $data;
    }

    private function bookable_seen()
    {
        $message = '';
        if (isset($_POST['remove_bookable']) && !empty($_POST['remove_bookable']) && $_POST['remove_bookable'] === 'remove') {
            $stmt = $this->conn->prepare('UPDATE bookable_changes b JOIN anb_url a ON b.id_url = a.id SET b.seen = 1 WHERE a.id_user = :id_user');
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->execute();
            $clean_book = $stmt->rowCount();

            $stmt = $this->conn->prepare('UPDATE price_changes p JOIN anb_url a ON p.id_url = a.id SET  p.seen = 1 WHERE a.id_user = :id_user');
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->execute();
            $clean_price = $stmt->rowCount();
            if ($clean_book > 0 || $clean_price > 0) {
                $message = '<div class="alert alert-warning" role="alert">Changes have been cleaned</div>';
                return $message;
            }
        }
        return $message;
    }

    function reorder_table()
    {
        if (isset($_REQUEST['reorder']) && $_REQUEST['reorder'] === 'true') {
            $stmt = $this->conn->prepare('SELECT id FROM anb_url WHERE id_user = :id_user');
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $stmt_upd = $this->conn->prepare('UPDATE anb_url SET order_main = :order_main WHERE id = :id AND id_user = :id_user');
            foreach ($res as $v) {
                if (isset($_REQUEST[$v])) {
                    $stmt_upd->bindValue(':id', (int)$v, PDO::PARAM_INT);
                    $stmt_upd->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
                    $stmt_upd->bindValue(':order_main', (int)$_REQUEST[$v], PDO::PARAM_STR);
                    $stmt_upd->execute();
                }
            }
            return '<div class="alert alert-success" role="alert">Rows has been reordered</div>';
        }
        return '';
    }

    function add_url()
    {
        $message = '';
        $own = 0;
        if (isset($_POST['own']) && $_POST['own'] === 'true') {
            $own = 1;
        }
        if (isset($_POST['add_url']) && !empty($_POST['add_url'])) {
            $stmt = $this->conn->prepare('SELECT id FROM anb_url WHERE url = :url AND id_user = :id_user');
            $stmt->bindValue(':url', trim($_POST['add_url']), PDO::PARAM_STR);
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $message = '<div class="alert alert-danger" role="alert">This page is already in the database</div>';
                return $message;
            }
            $stmt = $this->conn->prepare('SELECT COUNT(id) cn FROM anb_url WHERE  id_user = :id_user');
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ((int)$res['cn'] >= 27) {
                $message = '<div class="alert alert-danger" role="alert">You have a maximum of tracking apartments</div>';
                return $message;
            }
            $stmt = $this->conn->prepare('INSERT INTO anb_url SET url = :url, own = :own, id_user = :id_user');
            $stmt->bindValue(':url', trim($_POST['add_url']), PDO::PARAM_STR);
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->bindValue(':own', $own, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $message = '<div class="alert alert-success" role="alert">Page added successfully</div>';
                return $message;
            }
        }
        return $message;
    }

    function remove_url()
    {
        $message = '';
        if (isset($_POST['remove_url']) && !empty($_POST['remove_url'])) {
            $stmt = $this->conn->prepare('DELETE FROM anb_url WHERE id = :id AND id_user = :id_user');
            $stmt->bindValue(':id', (int)$_POST['remove_url'], PDO::PARAM_INT);
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $message = '<div class="alert alert-warning" role="alert">Page deleted successfully</div>';
                return $message;
            }
        }
        return $message;
    }

    function launch_parser()
    {
        $message = '';
        if (isset($_POST['launch']) && !empty($_POST['launch']) && $_POST['launch'] === 'true') {
            try {
                $locale = 'ru_RU.UTF-8';
                setlocale(LC_ALL, $locale);
                putenv('LC_ALL=' . $locale);
                $exec_command = 'java -jar ./parser/anb-1.0-jar-with-dependencies.jar anb ' . AuthController::$uid . ' > /dev/null &';
                exec($exec_command);
                $message = '<div class="alert alert-success" role="alert">The parser is running, to view the results, go to "View Logs"</div>';
            } catch (Exception $e) {
                $message = $e->getMessage();
            }
        }
        return $message;
    }

    function change_notes()
    {
        $message = '';
        if (isset($_POST['notes'], $_POST['id_notes'])) {
            $stmt = $this->conn->prepare('UPDATE anb_url SEt notes = :notes WHERE id = :id AND id_user = :id_user');
            $stmt->bindValue(':id', (int)$_POST['id_notes'], PDO::PARAM_INT);
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->bindValue(':notes', $_POST['notes']);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $message = '<div class="alert alert-success" role="alert">Notes has been updated successfully</div>';
                return $message;
            }
        }
        return $message;
    }

    public function get_list_url()
    {
        $data = [];
        $query = 'SELECT id, url, owner, own, num_parsing, suspend, apartment_name, notes, order_main FROM anb_url WHERE anb_url.id_user = :id_user ORDER BY order_main DESC, apartment_name ASC';
        $data_new = [];
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $r) {
            $stmt = $this->conn->prepare('SELECT price_was, price, date_cal, date_parsing, num_parsing, seen FROM price_changes WHERE seen = 0 AND id_url = :id_url');
            $stmt->bindValue(':id_url', (int)$r['id'], PDO::PARAM_INT);
            $stmt->execute();
            $res_price_change = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare('SELECT date_cal, date_parsing, num_parsing, seen FROM bookable_changes WHERE seen = 0 AND id_url = :id_url');
            $stmt->bindValue(':id_url', (int)$r['id'], PDO::PARAM_INT);
            $stmt->execute();
            $res_bookable_change = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $r['res_price_change'] = $res_price_change;
            $r['res_bookable_change'] = $res_bookable_change;

            $stmt = $this->conn->prepare('SELECT d.discount FROM discounts d WHERE d.id_url = :id ');
            $stmt->bindValue(':id', (int)$r['id'], PDO::PARAM_INT);
            $stmt->execute();
            $r['discounts'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($r['discounts'] as &$disc){
                $disc = str_replace(' price', '', $disc);
            }
            unset($disc);
            $r['min_nights'] = $this->get_min_nights($r['id']);
            $r['status_parsing'] = $this->status_url($r['suspend']);
            $data_new[] = $r;

        }
        $data['url_arr'] = $data_new;
        return $data;
    }

    private function get_URL($id)
    {
        $stmt = $this->conn->prepare('SELECT page_url_video FROM pages WHERE page_id = :id');
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['page_url_video'];
    }

    private function status_url($susp)
    {
        switch ((int)$susp) {
            case 0:
                return '<span class="text-success">OK</span>';
            case 1:
                return '<span class="text-warning">Unsuccessful parsing</span>';
            case 2:
                return '<span class="text-danger">is suspended</span>';
            default:
                return '';
        }
    }

    function get_min_nights($id)
    {
        $stmt = $this->conn->prepare('SELECT d.min_nights, d.date FROM anb_url a LEFT JOIN  checkup ch ON a.id = ch.iid_anb LEFT JOIN days d on ch.id = d.id_checkup WHERE a.id = :id ORDER BY d.date');
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        $buffer = [];
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $buffer[] = $result[0];
            $count_res = count($result);
            for ($i = 0; $i < $count_res; $i++) {
                if (end($buffer)['min_nights'] !== $result[$i]['min_nights']) {
                    $buffer[] = $result[$i - 1];
                    $buffer[] = $result[$i];
                }
            }
            $buffer[] = end($result);
        }
        return $buffer;

    }
}