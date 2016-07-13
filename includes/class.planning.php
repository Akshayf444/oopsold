<?php

require_once('database.php');

class Planning extends QueryWrapper {

    protected static $table_name = "planning";
    protected static $db_fields = array('empid', 'date', 'area', 'id', 'status', 'remark');
    public $empid;
    public $date;
    public $area;
    public $id;
    public $status;
    public $remark;

    public static function entryExist($date, $empid) {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE date = '{$date}' AND empid = '$empid' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_empid($empid) {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE empid = '$empid' AND status = '0' ");
        if (!empty($result_array)) {
            $rows = array();
            foreach ($result_array as $Planning) {
                $rows[] = array(
                    'id' => $Planning->id,
                    'title' => $Planning->area,
                    'start' => date('Y-m-d ', strtotime($Planning->date)),
                    'end' => date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime($Planning->date))),
                    'allDay' => true,
                );
            }

            return json_encode($rows);
        } else {
            return FALSE;
        }
    }

    public static function ListOfMissedDoctors($empid, $month) {
        $sql = "SELECT COUNT(dv.docid) AS visit_count ,db.`class`,pl.`date` ,d.* , dc.required_count FROM `doctors` d 
                LEFT JOIN doctor_visit dv ON d.`docid` = dv.`docid`
                LEFT JOIN doc_basic_profile db ON d.`docid` = db.`docid` 
                LEFT JOIN planning pl ON dv.`plan_id` = pl.`id` 
                INNER JOIN doctor_class dc ON db.class = dc.class
                WHERE d.`empid` = {$empid} AND db.`class` IS NOT NULL AND MONTH(pl.`date`) = {$month} GROUP BY d.docid";

        return QueryWrapper::executeQuery($sql);
    }

    public static function already_planned_dates_docwise($month , $docid){
        $sql = "SELECT GROUP_CONCAT(DAY(p.date)) AS VF FROM doctor_visit "
                . "dv INNER JOIN planning p ON dv.plan_id=p.id "
                . "INNER JOIN doctors d ON d.`docid`= dv.`docid` WHERE MONTH(p.date) = '$month' AND dv.docid='$docid'";
        $result_array = QueryWrapper::executeQuery($sql);
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }
    public static function DoctorWithClass($empid) {
        $doctors = array();
        $sql = "SELECT d.docid FROM doctors d LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` WHERE db.`class`  IS NOT NULL AND d.empid = '$empid' ";
        $result_array = QueryWrapper::executeQuery($sql);
        if (!empty($result_array)){
            foreach ($result_array as $value) {
                array_push($doctors, $value->docid);
            }
        }        
        return $doctors;
    }
    
    public static function NonVisitedDoctors($docid){
        $sql = " SELECT db.class,dc.required_count,d.* FROM `doc_basic_profile` db INNER JOIN doctor_class dc ON db.class = dc.class INNER JOIN `doctors` d ON db.docid = d.docid WHERE db.docid IN ($docid)";
        return QueryWrapper::executeQuery($sql);
    }

    public static function find_by_empid2($empid) {
        return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE empid = '$empid' ");
    }

    public static function find_by_id($id) {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE id = '$id' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    public static function find_by_date($month, $empid, $year) {
        return QueryWrapper::executeQuery("SELECT area FROM " . self::$table_name . " WHERE empid = '$empid' AND month(date) = '$month' AND year(date) = '$year' ");
    }

    public static function pagination($empid, $limit, $offset) {
        return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE empid = '$empid' LIMIT {$limit} OFFSET {$offset} ");
    }

    public static function pagination2($empid, $limit, $offset, $start_date) {
        $sql = "SELECT * FROM planning WHERE empid = '$empid' AND DATE = '$start_date'
                    UNION ALL
                SELECT * FROM planning WHERE empid = '$empid' AND DATE <> '$start_date' LIMIT {$limit} OFFSET {$offset} ";
        return QueryWrapper::executeQuery($sql);
    }

    public static function count_all($empid) {
        $result_array = QueryWrapper::executeQuery("SELECT COUNT(*) AS count FROM " . self::$table_name . " WHERE empid = '$empid' ");
        return array_shift($result_array);
    }

    public static function find_by_date_empid($date, $empid) {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE empid = '$empid' AND date = '$date' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    protected function attributes() {
        // return an array of attribute names and their values
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
        // sanitize the values before submitting
        // Note: does not alter the actual value of each attribute
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
        return QueryWrapper::executeCreate($sql);
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

    public function delete() {
        global $database;

        $sql = "DELETE FROM " . self::$table_name;
        $sql .= " WHERE id=" . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function updatePlanning() {
        $sql = "UPDATE " . self::$table_name . " SET bm_status = 1 WHERE empid = '$this->empid' AND date ='$this->date'   ";
        return QueryWrapper::executeQuery2($sql);
    }

}

?>