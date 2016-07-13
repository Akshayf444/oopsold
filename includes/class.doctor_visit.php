<?php

require_once('database.php');

class DoctorVisit extends QueryWrapper {

    protected static $table_name = "doctor_visit";
    protected static $db_fields = array('id', 'docid', 'empid', 'visit_date', 'plan_id');
    public $id;
    public $docid;
    public $empid;
    public $visit_date;
    public $plan_id;

    public static function find_by_id($id) {
        $sql = " select doctors.* , doctor_visit.* , priority_product.product1_id , priority_product.product2_id ,priority_product.product3_id "
                . "FROM doctors "
                . "inner join doctor_visit "
                . "on doctors.docid = doctor_visit.docid "
                . "inner join priority_product "
                . "on doctor_visit.docid = priority_product.docid "
                . "WHERE doctor_visit. plan_id = '{$id}' ";

        return QueryWrapper::executeQuery($sql);
    }

    public static function find_by_plan_id($plan_id) {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE plan_id = '$plan_id' ");
        return !empty($result_array) ? $result_array : FALSE;
    }

    public static function find_by_plan_id2($plan_id) {
        return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE plan_id = '$plan_id' ");
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
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

    public function create() {
        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . self::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        $result = QueryWrapper::executeCreate($sql);
        if ($result !== false) {
            return $result;
        } else {
            return false;
        }
    }
    
    public function delete(){
        $sql = " DELETE FROM ".self::$table_name." WHERE id = '$this->id' ";
        return QueryWrapper::executeQuery2($sql);
    }

}
