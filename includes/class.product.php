<?php
require_once('class.QueryWrapper.php');
class Product extends QueryWrapper {

    private static $table_name = 'product';

    public static function find_all() {
        if (isset($_SESSION['team']) && $_SESSION['team'] == 1) {
            return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE team = '1' ");
        } elseif (isset($_SESSION['team']) && $_SESSION['team'] == 2) {
            return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE team = '2' ");
        } else {
            return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name);
        }
    }

    public static function find_by_id($id) {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE id='{$id}'");
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

}
