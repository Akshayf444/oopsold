<?php

require_once('database.php');

class Employee {

    protected static $table_name = "employees";
    protected static $db_fields = array('empid', 'cipla_empid', 'name', 'emailid', 'password', 'mobile', 'city',
        'state', 'zone', 'region', 'HQ', 'bm_empid', 'lock_basic',
        'lock_service', 'lock_buisness', 'lock_academic', 'team',
        'DOB', 'doa', 'profile_photo', 'device_id');
    public $empid;
    public $cipla_empid;
    public $name;
    public $emailid;
    public $password;
    public $mobile;
    public $city;
    public $state;
    public $zone;
    public $region;
    public $HQ;
    public $bm_empid;
    public $lock_basic;
    public $lock_service;
    public $lock_buisness;
    public $lock_academic;
    public $team;
    public $DOB;
    public $doa;
    public $profile_photo;
    public $device_id;

    public static function authenticate($empid = "", $password = "") {
        global $database;
        $empid = $database->escape_value($empid);
        $password = $database->escape_value($password);

        $sql = "SELECT * FROM employees ";
        $sql .= "WHERE  cipla_empid = '{$empid}' ";
        $sql .= "AND  password = '{$password}' ";
        $sql .= "LIMIT 1";
        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    // TM Report filters
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_all_by_zone($zone) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE zone ='$zone' ");
    }

    public static function find_state($zone) {
        return self::find_by_sql("SELECT DISTINCT state FROM " . self::$table_name . " WHERE zone ='$zone' ");
    }

    public static function find_region($state) {
        return self::find_by_sql("SELECT DISTINCT region FROM " . self::$table_name . " WHERE state='$state' ");
    }

    public static function find_all_by_state($state) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE state ='$state' ");
    }

    public static function find_all_by_region($region) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE region='$region' ");
    }

    public static function find_by_bmid($bm_empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE bm_empid='$bm_empid'");
    }

    public static function find_by_buisness($buisness) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid IN (
			SELECT DISTINCT empid FROM doc_buisness_profile WHERE total < $buisness AND month(month) = {$_SESSION['_CURRENT_MONTH']}
		)");
    }

    public static function find_by_smname($sm_name) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE bm_empid IN(
		SELECT bm_empid FROM bm WHERE sm_empid IN (
			SELECT sm_empid FROM sm WHERE name = '{$sm_name}'
		)
		)");
    }

    //For replacing employee ids............
    public static function find_by_id($empid) {
        global $database;
        $result = $database->query("SELECT * FROM " . self::$table_name . " WHERE empid='$empid'");
        $row = $database->fetch_array($result);
        return $row;
    }

    public static function findReplace($empid) {
        global $database;
        $result = $database->query("SELECT * FROM " . self::$table_name . " WHERE bm_empid='$empid'");
        $row = $database->fetch_array($result);
        return $row;
    }

    public static function find_zone() {
        $sql = "SELECT DISTINCT(Zone) FROM " . self::$table_name;
        return QueryWrapper::executeQuery($sql);
    }

    public static function find_by_bmname($bmname) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE bm_empid IN(
		SELECT bm_empid FROM bm WHERE name = '{$bmname}' 
	)");
    }

    public static function find_by_smid($sm_empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE bm_empid IN(
		SELECT bm_empid FROM bm WHERE sm_empid = '{$sm_empid}'
	)");
    }

    public static function find_by_empid($empid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid='$empid' LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_sql($sql = "") {
       // echo $sql;
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    public static function count_basic_profile($empid) {
        $sql = "SELECT COUNT(*) FROM doc_basic_profile WHERE docid IN( SELECT docid from doctors Where empid ='{$empid}' ) ";
        return self::returnCount($sql);
    }

    public static function count_service($empid) {
        $sql = "SELECT COUNT(*) FROM services WHERE docid IN( SELECT docid from doctors Where empid ='{$empid}' ) ";
        return self::returnCount($sql);
    }

    public static function count_academic_profile($empid) {
        $sql = "SELECT COUNT(*) FROM doc_academic_profile WHERE docid IN( SELECT docid from doctors Where empid ='{$empid}' ) ";
        return self::returnCount($sql);
    }

    public static function count_all($bmempid) {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE bm_empid='$bmempid'";
        return self::returnCount($sql);
    }

    public static function count() {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name;
        return self::returnCount($sql);
    }

    public static function pagination($limit, $offset) {
        $sql = "SELECT * FROM " . self::$table_name . " LIMIT $limit OFFSET $offset";
        return self::find_by_sql($sql);
    }

    public static function returnCount($sql) {
        global $database;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    private static function instantiate($record) {
        $object = new self;
        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function has_attribute($attribute) {
        return array_key_exists($attribute, $this->attributes());
    }

    protected function attributes() {
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
        global $database;
        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . self::$table_name . " (";
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

    public function update($empid) {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE empid ='{$empid}'";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    //For admin
    public function SelectiveUpdate() {
        global $database;
        $sql = "UPDATE employees SET cipla_empid ='$this->cipla_empid' , password = '$this->password',emailid = '$this->emailid' , name = '$this->name' WHERE empid = '$this->empid' ";
        //echo $sql;
        $database->query($sql);
    }

    public function SelectiveReplace($empid, $fields) {
//        global $database;
//        $doctorProfiles = array('doctors', 'doc_basic_profile', 'services', 'doc_academic_profile', 'doc_buisness_profile', 'doctor_visit', 'planning', 'competitors', 'brand_business');
//        foreach ($doctorProfiles as $tablename) {
//            //Getting doctor id from doctors table
//            $result = Doctor::find_by_id($empid);
//            $result1 = Doctor::find_by_id($fields);
//            while ($row = $database->fetch_array($result)) {
//                //Replacing empid for each entry
//                $sql = "UPDATE $tablename SET empid = '$fields' WHERE docid = '{$row['docid']}' ";
//                $database->query($sql);
//            }
//
//            while ($row = $database->fetch_array($result1)) {
//                //Replacing empid for each entry
//                $sql = "UPDATE $tablename SET empid = '$empid' WHERE docid = '{$row['docid']}' ";
//                $database->query($sql);
//            }
//        }
//        $id = Employee::find_by_id($empid);
//        $id1 = Employee::find_by_id($fields);
//        $sql = "UPDATE employees SET empid ='$fields' WHERE id={$id['id']} ";
//        $database->query($sql);
//
//        $sql = "UPDATE employees SET empid ='$empid' WHERE id={$id1['id']} ";
//        $database->query($sql);
//        return ($database->affected_rows() == 1) ? true : false;
    }

    public function delete() {
        global $database;

        $sql = "DELETE FROM " . self::$table_name;
        $sql .= " WHERE id=" . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function sendsms($mobileNo, $message) {
        $smsUser = 'manish';
        $smsPassword = '123456';
        $var = "user=" . $smsUser . "&password=" . $smsPassword . "&senderid=MSPSGC&mobiles=" . $mobileNo . "&sms=" . $message;
        $curl = curl_init('http://trans.smsmojo.in/sendsms.jsp');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        $result = curl_exec($curl);
        curl_close($curl);
    }

    public function reportingChange($bm_empid, $empid) {
        global $database;
        $sql = "UPDATE employees SET bm_empid ='$bm_empid' WHERE empid = '$empid' ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function allNumbers() {
        $numbers = array();
        $allEmployee = Employee::find_all();
        foreach ($allEmployee as $employee) {
            array_push($numbers, $employee->mobile);
        }

        return $numbers;
    }

    public function lockBasic($empid, $status, $type = null) {
        if ($type == null) {
            $sql = "UPDATE " . self::$table_name . " SET lock_basic = $status WHERE empid = '$empid' ";
        } else {
            $sql = "UPDATE " . self::$table_name . " SET lock_basic = $status  ";
        }

        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function lockService($empid, $status, $type = null) {
        if ($type == null) {
            $sql = "UPDATE " . self::$table_name . " SET lock_service = $status WHERE empid = '$empid' ";
        } else {
            $sql = "UPDATE " . self::$table_name . " SET lock_service = $status ";
        }
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function lockBuisness($empid, $status, $type = null) {
        if ($type == null) {
            $sql = "UPDATE " . self::$table_name . " SET lock_buisness = $status WHERE empid = '$empid' ";
        } else {
            $sql = "UPDATE " . self::$table_name . " SET lock_buisness = $status  ";
        }
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function lockAcademic($empid, $status, $type = null) {
        if ($type == null) {
            $sql = "UPDATE " . self::$table_name . " SET lock_academic = $status WHERE empid = '$empid' ";
        } else {
            $sql = "UPDATE " . self::$table_name . " SET lock_academic = $status ";
        }
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function changePassword($newPassword, $empid) {
        $sql = "UPDATE " . self::$table_name . " SET password ='$newPassword' WHERE empid = '$empid' ";

        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updateMobile($empid, $mobile) {
        $sql = "UPDATE " . self::$table_name . " SET mobile ='$mobile' WHERE empid = '$empid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updatedob($empid, $dob) {
        $sql = "UPDATE " . self::$table_name . " SET DOB ='$dob' WHERE empid = '$empid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updatedoa($empid, $doa) {
        $sql = "UPDATE " . self::$table_name . " SET doa ='$doa' WHERE empid = '$empid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updatepic($empid, $path) {
        $sql = "UPDATE " . self::$table_name . " SET profile_photo ='$path' WHERE empid = '$empid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updateDevice($empid, $device_id) {
        $sql = "UPDATE " . self::$table_name . " SET device_id ='$device_id' WHERE empid = '$empid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function cipla_empid($empid) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE cipla_empid='$empid' LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

}

?>