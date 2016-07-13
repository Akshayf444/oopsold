<?php

require_once('database.php');
require_once('class.DatabaseObject.php');

class VisitApproval extends DatabaseObject {

    protected $table_name = "visit_approval";
    static $db_fields = array('id', 'empid', 'date', 'status');
    public $id;
    public $empid;
    public $date;
    public $status;

    // TM Report filters
    public function find_all() {
        return static::find_by_sql("SELECT * FROM " . $this->table_name . " ORDER BY state_name" , 'VisitApproval');
    }

    public function find_by_state_id($state_id = "") {
        $result_array = static::find_by_sql("SELECT * FROM " . $this->table_name . " WHERE state_id = '{$state_id}' " , 'VisitApproval');
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public function find_by_date_empid($date, $empid) {
        $result_array = static::find_by_sql("SELECT * FROM " . $this->table_name . " WHERE empid = '$empid' AND date ='$date' ", 'VisitApproval');
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . $this->table_name;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public function delete() {
        global $database;

        $sql = "DELETE FROM " . $this->table_name;
        $sql .= " WHERE id=" . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>