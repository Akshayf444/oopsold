<?php

require_once('database.php');

class QueryWrapper {
    public static function executeQuery($sql) {
        global $database;
        $result_set = $database->query($sql);
        //echo $sql;
        if ($result_set) {
            $object_array = array();

            while ($row = $database->fetch_associative_array($result_set)) {
                $object_array[] = self::instantiate($row);
            }
        }

        if (isset($object_array) && !empty($object_array)) {            
            return $object_array;
        } else {
            return FALSE;
        }
    }

    public static function executeQuery2($sql) {
        global $database;
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function executeCreate($sql) {
        global $database;
        if ($database->query($sql)) {
            return $database->insert_id();
        } else {
            return False;
        }
    }
    
    public static function ExcelData($sql){
        global $database;
        $result_set = $database->query($sql);
        return $result_set;
    }

    private static function instantiate($record) {
        $object = new self;
        foreach ($record as $attribute => $value) {
            $object->$attribute = $value;
        }
        return $object;
    }


}
