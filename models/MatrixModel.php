<?php
require_once 'Model.php';

class MatrixModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->create_connection();
    }

    public function get_data()
    {
        $data = [];
        $data['matrix'] = $this->get_8_months_matrix();
        return $data;
    }

    private function get_8_months_matrix()
    {
        $data = [];
        for ($i = 0; $i < 8; $i++) {
            $stmt = '';
            if ($i === 0) {
                $stmt = $this->conn->prepare("SELECT a.id, MONTH(d.date) month FROM anb_url a LEFT JOIN  checkup ch ON a.id = ch.iid_anb LEFT JOIN days d on ch.id = d.id_checkup WHERE a.id_user = :id_user AND MONTH(d.date) = MONTH(CURDATE()) GROUP BY a.id");

            } else {
                $stmt = $this->conn->prepare("SELECT a.id, MONTH(d.date) month FROM anb_url a LEFT JOIN  checkup ch ON a.id = ch.iid_anb LEFT JOIN days d on ch.id = d.id_checkup WHERE a.id_user = :id_user AND MONTH(d.date) = MONTH(DATE_ADD(CURDATE(), INTERVAL ${i} MONTH)) GROUP BY a.id");
            }
            $stmt->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
            $stmt->execute();
            $id_urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($id_urls as $id_url) {
                $stmt_url = $this->conn->prepare("SELECT a.id, a.url, a.apartment_name, d.date, d.bookable, d.available FROM anb_url a LEFT JOIN  checkup ch ON a.id = ch.iid_anb LEFT JOIN days d on ch.id = d.id_checkup WHERE a.id_user = :id_user AND MONTH(d.date) = :month AND a.id = :id_url");
                $stmt_url->bindValue(':id_user', AuthController::$uid, PDO::PARAM_INT);
                $stmt_url->bindValue(':month', (int)$id_url['month'], PDO::PARAM_INT);
                $stmt_url->bindValue(':id_url', (int)$id_url['id'], PDO::PARAM_INT);
                $stmt_url->execute();
                $res_month = $stmt_url->fetchAll(PDO::FETCH_ASSOC);
                foreach ($res_month as &$m){
                    $m['date'] = new DateTime($m['date']);
                }
                unset($m);
                $data[$i][] = $res_month;
            }
        }
        return $data;
    }
}
