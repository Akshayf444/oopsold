<?php
require_once('database.php');

class SMS {

    protected static $table_name = "sent_sms";
    public $date;
    public $msg;
    public $msg_count;
    
    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE month(date)=month(CURDATE())";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_per_month($month) {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE month(date) = $month AND YEAR(date) = YEAR(CURDATE()) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['0'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public function create() {
        global $database;
        $sql = "INSERT INTO sent_sms (date,msg,msg_count) VALUES ('$this->date' , '$this->msg' , '$this->msg_count') ";
        if ($database->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function update($docid) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE docid='$docid'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>