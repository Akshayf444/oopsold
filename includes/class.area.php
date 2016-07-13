<?php

class Area extends QueryWrapper {
    protected static $table_name="area";

    public static function find_all() {
        $areaList = array();
        $sql = "SELECT area FROM area";
        $result = QueryWrapper::executeQuery($sql);
        foreach ($result as $areaname) {
            array_push($areaList, $areaname->area);
        }
        return json_encode($areaList);
    }

}
