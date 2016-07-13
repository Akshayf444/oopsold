<?php

date_default_timezone_set("Asia/Kolkata");
require_once('database.php');

class BrandBusiness {

    protected static $table_name = "brand_business";
    protected static $db_fields = array('id', 'docid', 'empid', 'brand1', 'brand2', 'brand3', 'brand4', 'brand5', 'brand6', 'brand7', 'brand8', 'month', 'total');
    public $id;
    public $docid;
    public $empid;
    public $brand1 = 0;
    public $brand2 = 0;
    public $brand3 = 0;
    public $brand4 = 0;
    public $brand5 = 0;
    public $brand6 = 0;
    public $brand7 = 0;
    public $brand8 = 0;
    public $month = 0;
    public $total = 0;

    public static function find_by_sql($sql = "") {
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    public static function entryExist($docid) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(month) ='{$_SESSION['_CURRENT_MONTH']}' AND docid = '$docid' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    public static function find_by_month($month, $empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(month) = '$month' AND empid = '$empid' ");
    }

    public static function find_all_by_month($month) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(month) = '$month'  ");
    }

    public static function find_by_month_sm($month, $sm_empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(month) = '$month' AND empid IN(  SELECT empid from employees Where bm_empid IN(
      			SELECT bm_empid FROM bm WHERE sm_empid='{$sm_empid}'
      	)) ");
    }

    public static function find_by_month_bm($month, $bm_empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(month) = '$month' AND empid IN(  SELECT empid from employees Where bm_empid = '$bm_empid' ) ");
    }

    public static function find_by_date($month, $empid) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(month) ='{$_SESSION['_CURRENT_MONTH']}' AND empid = '$empid' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    public static function find_by_docid($month, $docid) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(month) = '$month' AND docid = '$docid' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(id) FROM " . self::$table_name;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    private static function instantiate($record) {
        $object = new self;
        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function has_attribute($attribute) {
        return array_key_exists($attribute, $this->attributes());
    }

    protected function attributes() {
        $attributes = array();
        foreach (self::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    protected function sanitized_attributes() {
        global $database;
        $clean_attributes = array();
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

    public function create() {
        global $database;
        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . self::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        if ($database->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function update() {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id ='{$this->id}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>