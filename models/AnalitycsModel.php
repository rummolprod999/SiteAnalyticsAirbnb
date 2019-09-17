<?php
require_once 'Model.php';

class AnalitycsModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->create_connection();
    }

    public function get_data()
    {
        $data = [];
        $query = 'SELECT a.start_date, a.end_date FROM analitic a WHERE a.perid_nights = 6 GROUP BY a.start_date, a.end_date ORDER BY a.start_date';
        $res = $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $r) {
            $stmt = $this->conn->prepare('SELECT a.start_date, a.end_date, a.price, au.id, au.own  FROM analitic a JOIN anb_url au on a.id_url = au.id WHERE a.start_date = STR_TO_DATE(:st, \'%Y-%m-%d\') AND a.end_date = STR_TO_DATE(:en, \'%Y-%m-%d\') ORDER BY  a.price ASC');
            $stmt->bindValue(':st', $r['start_date'], PDO::PARAM_STR);
            $stmt->bindValue(':en', $r['end_date'], PDO::PARAM_STR);
            $stmt->execute();
            $res_inner = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $periods = [];
            foreach ($res_inner as $n) {
                $periods[] = $n;
            }
            $data[] = $periods;
        }
        return $data;
    }
}