<?php

class DailyCallPlanning extends QueryWrapper {

    protected static $table_name = "daily_call_planning";
    protected static $db_fields = array('id', 'docid', 'plan_id', 'input', 'service', 'activity', 'pob', 
                                        'post_call_planning' ,'meet' , 'reason' ,'created');
    public $id;
    public $docid;
    public $plan_id;
    public $input;
    public $service;
    public $activity;
    public $pob;
    public $post_call_planning;
    public $meet;
    public $reason;
    public $created;
    
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

    public static function find_by_id($id = "") {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE id ='$id' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    protected function sanitized_attributes() {
        global $database;
        $clean_attributes = array();
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

    public static function find_by_plan_id($plan_id) {
        return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE plan_id ='$plan_id' ");
    }

    public static function lastMeet($docid){
        //$empid = $_SESSION['employee'];
        $sql = "SELECT * FROM ". self::$table_name ." WHERE docid ='$docid' ORDER BY created DESC LIMIT 1";
        $result_array = QueryWrapper::executeQuery($sql);
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }
    public static function find_by_plan_id2($plan_id) {
        $sql = " select doctors.* , daily_call_planning.* , priority_product.* "
                . "FROM doctors "
                . "inner join daily_call_planning "
                . "on doctors.docid = daily_call_planning.docid "
                . "inner join priority_product "
                . "on doctors.docid = priority_product.docid "
                . "WHERE daily_call_planning.plan_id = '{$plan_id}' ";

        $result_array = QueryWrapper::executeQuery($sql);
        return $result_array;
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
