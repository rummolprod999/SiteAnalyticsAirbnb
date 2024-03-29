<?php
require_once 'Model.php';

class ChangesModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->create_connection();
    }

    public function get_info_url($id_url)
    {
        $data = [];
        $data['descr'] = $this->get_description($id_url);
        $data['bookable_changes'] = $this->get_changes_bookable($id_url);
        $data['price_changes'] = $this->get_changes_price($id_url);

        $data['video_url'] = $this->get_URL(5);
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

    private function get_description($id_url)
    {
        $stmt = $this->conn->prepare('SELECT a.apartment_name FROM anb_url a WHERE a.id = :id');
        $stmt->bindValue(':id', (int)$id_url, PDO::PARAM_INT);
        $stmt->execute();
        $dt = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dt;
    }

    private function get_changes_bookable($id_url)
    {
        if (isset($_GET['date_start'], $_GET['date_end'])) {
            $stmt = $this->conn->prepare("SELECT b.date_parsing, b.date_cal, b.price FROM bookable_changes b WHERE b.date_parsing BETWEEN STR_TO_DATE(:st, '%Y-%m-%d') AND STR_TO_DATE(:en, '%Y-%m-%d') AND b.id_url = :id ORDER BY b.date_parsing");
            $stmt->bindValue(':id', (int)$id_url, PDO::PARAM_INT);
            $stmt->bindValue(':st', $_GET['date_start'], PDO::PARAM_STR);
            $stmt->bindValue(':en', $_GET['date_end'], PDO::PARAM_STR);
            $stmt->execute();
            $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $dates;
        }
        $stmt = $this->conn->prepare('SELECT b.date_parsing, b.date_cal, b.price FROM bookable_changes b WHERE (b.date_parsing BETWEEN DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AND b.id_url = :id ORDER BY b.date_parsing');
        $stmt->bindValue(':id', (int)$id_url, PDO::PARAM_INT);
        $stmt->execute();
        $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $dates;
    }

    private function get_changes_price($id_url)
    {
        if (isset($_GET['date_start'], $_GET['date_end'])) {
            $stmt = $this->conn->prepare("SELECT p.date_parsing, p.date_cal, p.price_was, p.price FROM price_changes p WHERE p.date_parsing BETWEEN STR_TO_DATE(:st, '%Y-%m-%d') AND STR_TO_DATE(:en, '%Y-%m-%d') AND p.id_url = :id ORDER BY p.date_parsing");
            $stmt->bindValue(':id', (int)$id_url, PDO::PARAM_INT);
            $stmt->bindValue(':st', $_GET['date_start'], PDO::PARAM_STR);
            $stmt->bindValue(':en', $_GET['date_end'], PDO::PARAM_STR);
            $stmt->execute();
            $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $dates;
        }

        $stmt = $this->conn->prepare('SELECT p.date_parsing, p.date_cal, p.price_was, p.price FROM price_changes p WHERE (p.date_parsing BETWEEN DATE_SUB(CURDATE(), INTERVAL 2 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AND p.id_url = :id ORDER BY p.date_parsing');
        $stmt->bindValue(':id', (int)$id_url, PDO::PARAM_INT);
        $stmt->execute();
        $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $dates;
    }
}