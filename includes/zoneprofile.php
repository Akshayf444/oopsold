<?php

require_once('database.php');

class zone {

    protected static $table_name = "zonal_manager";
    protected static $db_fields = array('zone', 'name', 'emailid', 'password', 'mobile', 'hq');
    public $zone_id;
    public $name;
    public $emailid;
    public $password;
    public $mobile;
    public $zone;
    public $hq;

//    public static function find() {
//        return self::find_by_sql("SELECT * FROM " . self::$table_name);
//    }
//
//    // Common Database Methods
//    public static function find_all($empid = '') {
//        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE sm_empid='$empid' ");
//    }

    public static function find_by_zoneid($zone_id = "") {
        $result_array = QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE zone_id='$zone_id' LIMIT 1");

        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function count_all($zone_id) {
        $sql = "SELECT COUNT(*) FROM employees WHERE zonal_id='$zone_id'";

        return self::returnCount($sql);
    }

    public static function view_all($zone_id) {
        $sql = "SELECT * FROM employees WHERE zonal_id='$zone_id'";

        return self::returnCount($sql);
    }

    public static function returnCount($sql) {
        global $database;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function find_by_bmid($bm_empid) {
        $sql = "SELECT * FROM employees WHERE zonal_id='$bm_empid'";
        return QueryWrapper::executeQuery($sql);
    }

    public static function count_all_doc($empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doctors WHERE empid='$empid' AND is_delete = 0 ";
        return self::returnCount($sql);
    }

    public static function find_all() {
        $sql = "SELECT COUNT(*) FROM doctors  WHERE is_delete = 0 AND empid != '' ";
        return self::returnCount($sql);
    }

    public static function count_all_doctors($zone_id) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doctors WHERE empid IN( SELECT empid from employees Where zonal_id='{$zone_id}' ) AND is_delete = 0 ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_basic_profile($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_basic_profile WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE zonal_id ='{$bm_empid}' 
            ) ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_buisness_profile($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_buisness_profile WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE zonal_id ='{$bm_empid}' 
            ) ) AND MONTH(month) = MONTH(CURDATE()) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_service($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM services WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE zonal_id ='{$bm_empid}' 
            ) ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_academic_profile($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_academic_profile WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE zonal_id  ='{$bm_empid}' 
            ) ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function totalProfileCount($zone_id) {
        $id = $zone_id;
        $basicCount = BM:: count_basic_profile($id);
        $buisnessCount = BM:: count_buisness_profile($id);
        $academicCount = BM:: count_academic_profile($id);
        $serviceCount = BM:: count_service($id);
        return $basicCount + $buisnessCount + $academicCount + $serviceCount;
    }

    public static function count_basic_profile_doc($docid) {
        global $database;
        $errorcount = 0;
        $sql = "SELECT * FROM doc_basic_profile WHERE docid ={$docid} ";
        $basicProfile = QueryWrapper::executeQuery($sql);
        if (!empty($basicProfile)) {
            $basicProfile = array_shift($basicProfile);
            $fields = BasicProfile::$db_fields;

            $exclude = array('other', 'any_other', 'month', 'clinic_address', 'residential_address', 'DOB', 'DOA', 'plot1', 'street1', 'pincode1', 'plot2', 'street2', 'pincode2');
            $fields = array_diff($fields, $exclude);

            foreach ($fields as $field) {
                if ($basicProfile->{$field} == '') {
                    $errorcount ++;
                }
            }

            if ($basicProfile->DOB == '0000-00-00') {
                $errorcount ++;
            }

            if ($errorcount == 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function count_buisness_profile_doc($docid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_buisness_profile WHERE docid ={$docid} AND MONTH(month) = {$_SESSION['_CURRENT_MONTH']} ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        $count = array_shift($row);
        return $count === 0 ? 0 : 1;
    }

    public static function count_competitor_doc($docid) {
        $sql = "SELECT COUNT(*) FROM competitors WHERE docid ={$docid}";
        return self::returnCount($sql);
    }

    public static function count_service_doc($docid) {
        global $database;
        $errorcount = 0;
        $sql = "SELECT * FROM services WHERE docid = {$docid} ";
        $services = QueryWrapper::executeQuery($sql);
        if (!empty($services)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function count_academic_profile_doc($docid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_academic_profile WHERE docid ={$docid} ";
        return self::returnCount($sql);
    }

    public static function totalProfileCount_doc($docid) {
        $id = $docid;
        $basicCount = Doctor:: count_basic_profile_doc($id);
        //$buisnessCount = Doctor:: count_buisness_profile($id);
        $academicCount = Doctor:: count_academic_profile_doc($id);
        $serviceCount = Doctor:: count_service_doc($id);
        $competitorCount = Doctor::count_competitor_doc($id);
        //return $basicCount + $buisnessCount + $academicCount + $serviceCount + $competitorCount;
        return $basicCount + $academicCount + $serviceCount + $competitorCount;
    }

    public static function allProfileCount($empid) {
        $sql = " SELECT 
                    COUNT(da.`docid`) AS academic , COUNT(ser.`docid`) service , COUNT(`cmpt`.`docid`) compt

                   FROM `employees` e 
                   LEFT JOIN doctors d 
                   ON d.`empid` = e.`empid`
                       LEFT JOIN services ser 
                       ON d.`docid` = ser.`docid` 
                     LEFT JOIN `doc_academic_profile` da 
                       ON d.`docid` = da.`docid` 
                     LEFT JOIN competitors cmpt 
                       ON d.`docid` = cmpt.`docid` 
                       WHERE d.is_delete = 0 AND d.`empid` = {$empid}";
        $result_array = QueryWrapper::executeQuery($sql);
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }

    public static function bm_name($zone_id) {

        $sql = " SELECT 
                    bm.name as name ,employees.name as emp_name,employees.empid
                   FROM `bm`
                   LEFT JOIN `employees`
                   ON bm.`bm_empid` = `employees`.`bm_empid` WHERE `employees`.zonal_id=$zone_id";
        return QueryWrapper::executeQuery($sql);
    }

    public static function find_all_doctors($bm_empid) {
        global $database;
        $sql = "SELECT * FROM doctors WHERE empid IN( SELECT empid from employees Where empid='{$bm_empid}' ) AND is_delete = 0  ";
        return QueryWrapper::executeQuery($sql);
    }

    public static function find_all_tm($zone_id) {
        global $database;
        $sql = "select * from employees where zonal_id='$zone_id'";
        
        return QueryWrapper::executeQuery($sql);
    }

    public static function BMdrawPieChart($zone_id, $class) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `doc_basic_profile` WHERE empid IN (
        SELECT empid FROM employees WHERE zonal_id ='{$zone_id}'
      ) AND CLASS='$class' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function BMdrawPieChart2($zone_id, $service) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `services` WHERE empid IN (
		SELECT empid FROM employees WHERE zonal_id ='{$zone_id}'
	) AND service ='$service' ";

        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function BMdrawBarGraph($zone_id, $total) {
        global $database;
        $sql = " SELECT COUNT(*) FROM (
                    SELECT SUM(total) FROM doc_buisness_profile WHERE empid IN(  
			SELECT empid FROM employees WHERE zonal_id = '{$zone_id}'
                    ) AND MONTH(MONTH) = {$_SESSION['_CURRENT_MONTH']} GROUP BY `docid` HAVING $total 
                ) AS t";
        return self::returnCount($sql);
    }
    

//    public static function Month_wise_Buisness($month, $zoneid) {
//        $sql = " SELECT SUM(total) AS SUM1  FROM doc_buisness_profile WHERE month(month) ={$month} AND zonal_id = '$zoneid' ";
//        return self::returnCount($sql);
//    }
 public static function count_by_bm_zoneid($bm_empid) {
        $sql = "SELECT COUNT(*) FROM activity_details WHERE doc_id IN (
  		SELECT docid FROM doctors WHERE empid IN (
                SELECT empid FROM employees WHERE zonal_id = '$bm_empid'
                )
  	)";
        return self::returnCount($sql);
    }
      public static function count_for_month($zone_id, $month) {
        $sql = "SELECT COUNT(*) FROM activity_details WHERE MONTH(activity_date) = '$month' AND doc_id IN (
  				SELECT docid FROM doctors WHERE empid IN (
                SELECT empid FROM employees WHERE zonal_id = '$zone_id')
                )";
        return self::returnCount($sql);
    }
    public static function Month_wise_Buisness($month, $zone_id) {
        $sql = " SELECT SUM(total) AS SUM1  FROM  doc_buisness_profile WHERE month(month) ='$month' AND empid IN(  SELECT empid FROM employees WHERE zonal_id = '$zone_id')
                ";
        return self::returnCount($sql);
    }

}

?>
