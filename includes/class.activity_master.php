<?php
class ActivityMaster extends QueryWrapper{
    
    public static function find_all(){
        return QueryWrapper::executeQuery("SELECT * FROM activity_master");
    }
    
    public static function find_by_id($id){
        $result_array = QueryWrapper::executeQuery("SELECT * FROM activity_master WHERE id = '$id' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }
}
