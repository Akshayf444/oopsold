<?php

require_once('database.php');

class Activity {

    protected static $table_name = "activity_details";
    protected static $db_fields = array('act_id', 'doc_id', 'activity_type', 'activity_date', 'highlight', 'expances', 'brand1', 'brand2', 'brand3', 'brand4', 'brand5', 'brand6', 'brand7', 'brand8', 'total', 'empid', 'filename');
    public $act_id;
    public $doc_id;
    public $activity_type;
    public $activity_date;
    public $highlight;
    public $expances;
    public $brand1 = 0;
    public $brand2 = 0;
    public $brand3 = 0;
    public $brand4 = 0;
    public $brand5 = 0;
    public $brand6 = 0;
    public $brand7 = 0;
    public $brand8 = 0;
    public $total = 0;
    public $empid;
    public $filename;

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_all_by_zone($zone) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE zone ='$zone' ");
    }

    public static function find_by_empid($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE doc_id IN (
  				SELECT docid FROM doctors WHERE empid = '$empid'
  			)");
    }

    public static function find_by_month($empid, $activity, $month) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE activity_type = '$activity' AND month(activity_date) = '$month' AND doc_id IN (
  				SELECT docid FROM doctors WHERE empid = '$empid'
  			)");
    }

    public static function find_all_by_month($activity, $month) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE activity_type = '$activity' AND month(activity_date) = '$month' ");
    }

    public static function count_by_month($empid, $activity, $month) {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE activity_type = '$activity' AND month(activity_date) = '$month' AND doc_id IN (
  				SELECT docid FROM doctors WHERE empid = '$empid'
  			)";
        return self::returnCount($sql);
    }

    public static function count_by_empid($empid) {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE doc_id IN (
  		SELECT docid FROM doctors WHERE empid = '$empid'
  	)";
        return self::returnCount($sql);
    }

    public static function count_all() {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name;
        return self::returnCount($sql);
    }

    public static function count_for_month($empid, $month) {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE month(activity_date) = '$month' AND doc_id IN (
  				SELECT docid FROM doctors WHERE empid = '$empid'
  			)";
        return self::returnCount($sql);
    }

    public static function find_topper() {
        $sql = "SELECT empid, COUNT(*) as Activity_count FROM `activity_details` GROUP BY empid ORDER BY COUNT(*) DESC LIMIT 5";
        return QueryWrapper::executeQuery($sql);
    }

    public static function count_all_by_month($month) {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE month(activity_date) = '$month' ";
        return self::returnCount($sql);
    }

    public static function count_by_bm_empid($bm_empid) {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE doc_id IN (
  		SELECT docid FROM doctors WHERE empid IN (
                SELECT empid FROM employees WHERE bm_empid = '$bm_empid'
                )
  	)";
        return self::returnCount($sql);
    }

    public static function count_by_sm_empid($sm_empid) {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE doc_id IN (
  		SELECT docid FROM doctors WHERE empid IN (
                SELECT empid FROM employees WHERE bm_empid IN (
                SELECT bm_empid FROM bm WHERE sm_empid = '$sm_empid'
                )
            )
  	)";
        return self::returnCount($sql);
    }

    public static function find_by_actid($act_id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE act_id='$act_id' LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_sql($sql = "") {
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    public static function returnCount($sql) {
        global $database;
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
            return $database->insert_id();
        } else {
            return false;
        }
    }

    public function update($empid) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE act_id ='{$empid}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    //For admin

    public function autoGenerate_id() {

        $num = self::count_all();
        ++$num; // add 1;
        return 'ACT' . $num;
    }

    public static function getDetails() {
        $sql = "select act.*,doc.name,doc_basic.msl_code,am.activity as master_type from `activity_details` act left join`activity_master` am on act.activity_type=am.id left join`doctors` doc on act.doc_id =doc.docid left join doc_basic_profile doc_basic on act.doc_id=doc_basic.docid";
        return QueryWrapper::executeQuery($sql);
    }

    static function buildQuery($conditions) {
        $sql = "select act.*,doc.name,doc_basic.msl_code,am.activity as master_type from `activity_details` act "
                . " left join`activity_master` am on act.activity_type=am.id "
                . " left join`doctors` doc on act.doc_id =doc.docid "
                . " left join doc_basic_profile doc_basic on act.doc_id=doc_basic.docid ";
        if (!empty($conditions)) {
            $sql .= join(" ", $conditions);
        }

        return $sql;
    }

}

?>