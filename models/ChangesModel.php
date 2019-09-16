<?php
require_once 'Model.php';

class ChangesModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->create_connection();
    }

    private function  get_changes_bookable($id_url){
        if(isset($_GET['date_start'], $_GET['date_end'])){
            $stmt = $this->conn->prepare("SELECT b.date_parsing, b.date_cal FROM bookable_changes b WHERE b.date_parsing BETWEEN STR_TO_DATE(:st, '%Y-%m-%d') AND STR_TO_DATE(:en, '%Y-%m-%d') AND b.id_url = :id ORDER BY b.date_parsing");
            $stmt->bindValue(':id', (int)$id_url, PDO::PARAM_INT);
            $stmt->bindValue(':st', $_GET['date_start'], PDO::PARAM_STR);
            $stmt->bindValue(':en', $_GET['date_end'], PDO::PARAM_STR);
            $stmt->execute();
            $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $dates;
        }
        return null;
    }
    private function  get_changes_price($id_url){
        if(isset($_GET['date_start'], $_GET['date_end'])){
            $stmt = $this->conn->prepare("SELECT p.date_parsing, p.date_cal, p.price_was, p.price FROM price_changes p WHERE p.date_parsing BETWEEN STR_TO_DATE(:st, '%Y-%m-%d') AND STR_TO_DATE(:en, '%Y-%m-%d') AND p.id_url = :id ORDER BY p.date_parsing");
            $stmt->bindValue(':id', (int)$id_url, PDO::PARAM_INT);
            $stmt->bindValue(':st', $_GET['date_start'], PDO::PARAM_STR);
            $stmt->bindValue(':en', $_GET['date_end'], PDO::PARAM_STR);
            $stmt->execute();
            $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $dates;
        }
        return null;
    }

    public function get_info_url($id_url)
    {
        $data = [];
        $data['bookable_changes'] = $this->get_changes_bookable($id_url);
        $data['price_changes'] = $this->get_changes_price($id_url);
        return $data;
    }
}