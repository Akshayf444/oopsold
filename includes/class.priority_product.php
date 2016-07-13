<?php

class PriorityProduct extends QueryWrapper {

    private static $table_name = 'priority_product';
    protected static $db_fields = array('id', 'docid', 'product1_id', 'product2_id', 'product3_id');
    public $id;
    public $docid;
    public $product1_id;
    public $product2_id;
    public $product3_id;

    public static function find_all($empid) {
        return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name );
    }

    public static function  find_by_id($id){
       $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE id ='$id' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE; 
    }

    public static function find_by_docid($docid) {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE docid ='$docid' ");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    private function has_attribute($attribute) {

        return array_key_exists($attribute, $this->attributes());
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
        $sql .= " WHERE id={$this->id}";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}
