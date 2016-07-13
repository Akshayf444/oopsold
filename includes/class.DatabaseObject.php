<?php

//Late static binding implementation of PHP 5.3

class DatabaseObject {

    static $db_fields = array();

    static function instantiate($record, $classname) {
        $object = new $classname();
        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    public function attributes() {
        $attributes = array();

        foreach (static::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    static function find_by_sql($sql = "", $classname) {
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = static::instantiate($row, $classname);
        }
        return $object_array;
    }

    public function has_attribute($attribute) {
        return array_key_exists($attribute, $this->attributes());
    }

    public function sanitized_attributes() {
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
        $sql = "INSERT INTO " . $this->table_name . " (";
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
        $sql = "UPDATE " . $this->table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id ='{$this->id}'";
        //echo $sql;
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}
