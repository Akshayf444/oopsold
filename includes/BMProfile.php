<?php

require_once('database.php');

class BM {

    protected static $table_name = "bm";
    protected static $db_fields = array('bm_empid', 'name', 'emailid', 'password', 'mobile', 'sm_empid', 'team', 'profile_photo', 'DOB', 'doa');
    public $bm_empid;
    public $name;
    public $emailid;
    public $password;
    public $mobile;
    public $sm_empid;
    public $team;
    public $profile_photo;
    public $DOB;
    public $doa;

    public static function authenticate($docid = "", $password = "") {
        global $database;
        $docid = $database->escape_value($docid);
        $password = $database->escape_value($password);
        $sql = "SELECT * FROM bm ";
        $sql .= "WHERE bm_empid = '{$docid}' ";
        $sql .= "AND password = '{$password}' ";
        $sql .= "LIMIT 1";
        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    // Common Database Methods
    public static function find_all($empid = '') {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE sm_empid='$empid' ");
    }

    public static function find_by_bmid($bm_empid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE bm_empid='$bm_empid' LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    //For replacing BM ids............
    public static function find_by_id($bmempid) {
        global $database;
        $result = $database->query("SELECT * FROM " . self::$table_name . " WHERE bm_empid='$bmempid'");
        $row = $database->fetch_array($result);
        return $row;
    }

    public static function findReplace($smempid) {
        global $database;
        $result = $database->query("SELECT * FROM " . self::$table_name . " WHERE sm_empid='$smempid'");
        $row = $database->fetch_array($result);
        return $row;
    }

    public static function find_by_sql($sql = "") {
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    //Counting Methods
    public static function count_all($empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE sm_empid='$empid'";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_all_doctors($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doctors WHERE empid IN( SELECT empid from employees Where bm_empid='{$bm_empid}' ) AND is_delete = 0 ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_all_employees($sm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM employees WHERE bm_empid IN( SELECT bm_empid from bm Where sm_empid='{$sm_empid}' ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_basic_profile($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_basic_profile WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE bm_empid ='{$bm_empid}' 
            ) ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_buisness_profile($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_buisness_profile WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE bm_empid  ='{$bm_empid}' 
            ) ) AND MONTH(month) = MONTH(CURDATE()) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_service($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM services WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE bm_empid  ='{$bm_empid}' 
            ) ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function count_academic_profile($bm_empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_academic_profile WHERE docid IN( SELECT docid from doctors Where empid IN(
      			SELECT empid FROM employees WHERE bm_empid  ='{$bm_empid}' 
            ) ) ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function totalProfileCount($bmempid) {
        $id = $bmempid;
        $basicCount = BM:: count_basic_profile($id);
        $buisnessCount = BM:: count_buisness_profile($id);
        $academicCount = BM:: count_academic_profile($id);
        $serviceCount = BM:: count_service($id);
        return $basicCount + $buisnessCount + $academicCount + $serviceCount;
    }

    //Assigning fetched values to variables
    private static function instantiate($record) {
        // Could check that $record exists and is an array
        $object = new self;

        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function has_attribute($attribute) {
        // We don't care about the value, we just want to know if the key exists
        // Will return true or false
        return array_key_exists($attribute, $this->attributes());
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

    /*
      public function update() {
      global $database;

      // - single-quotes around all values
      // - escape all values to prevent SQL injection
      $attributes = $this->sanitized_attributes();
      $attribute_pairs = array();
      foreach($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
      }
      $sql = "UPDATE ".self::$table_name." SET ";
      $sql .= join(", ", $attribute_pairs);
      $sql .= " WHERE docid=". $database->escape_value($this->docid);
      $database->query($sql);
      return ($database->affected_rows() == 1) ? true : false;
      }

      /*public static function last_record() {
      return self::find_by_sql("SELECT * FROM ".self::$table_name."  order by docid desc LIMIT 1 ");
      } */

    /* public function delete() {
      global $database;

      $sql = "DELETE FROM ".self::$table_name;
      $sql .= " WHERE id=". $database->escape_value($this->id);
      $sql .= " LIMIT 1";
      $database->query($sql);
      return ($database->affected_rows() == 1) ? true : false;

      }

      public function autoGenerate_id(){
      global $database;
      $sql="SELECT * FROM ".self::$table_name."  order by id desc LIMIT 1 ";
      $result=$database->query($sql);
      $row=mysqli_fetch_array($result);

      $num=substr($row[0],2);

      ++$num; // add 1;
      $final= 'DC'.$num;
      if($final=='DC'){return $final.'0';}
      else{return $final;}
      } */

    public function SelectiveUpdate($empid, $fields) {
        global $database;
        $sql = "UPDATE employees SET bm_empid = '$fields[0]' WHERE bm_empid = '$empid'";
        $database->query($sql);
        $sql = "UPDATE " . self::$table_name . " SET bm_empid ='$fields[0]', name ='$fields[1]',emailid ='$fields[2]', password ='$fields[3]',mobile ='$fields[4]' ";
        $sql .= " WHERE bm_empid = '{$empid}' ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function SelectiveReplace($empid, $fields) {
//        global $database;
//
//        //Getting BM id from BM table
//        $result = Employee::findReplace($empid);
//        $result1 = Employee::findReplace($fields);
//        while ($row = $database->fetch_array($result)) {
//            //Replacing BM for each entry
//            $sql = "UPDATE employees SET bm_empid = '$fields' WHERE empid = '{$row['empid']}' ";
//            $database->query($sql);
//        }
//
//        while ($row = $database->fetch_array($result1)) {
//            //Replacing BM for each entry
//            $sql = "UPDATE employees SET bm_empid = '$empid' WHERE empid = '{$row['empid']}' ";
//            $database->query($sql);
//        }
//
//        $id = BM::find_by_id($empid);
//        $id1 = BM::find_by_id($fields);
//        $sql = "UPDATE bm SET bm_empid ='$fields' WHERE id={$id['id']} ";
//        $database->query($sql);
//
//        $sql = "UPDATE bm SET bm_empid ='$empid' WHERE id={$id1['id']} ";
//        $database->query($sql);
//        return ($database->affected_rows() == 1) ? true : false;
    }

    public function reportingChange($fields) {
        global $database;
        $sql = "UPDATE bm SET sm_empid ='$fields[0]' WHERE bm_empid = '$fields[1]' ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function allNumbers() {
        $numbers = array();
        $allBM = BM::find();
        foreach ($allBM as $BM) {
            array_push($numbers, $BM->mobile);
        }
        return $numbers;
    }

    public static function changePassword($newPassword, $empid) {
        $sql = "UPDATE " . self::$table_name . " SET password ='$newPassword' WHERE bm_empid = '$empid' ";

        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updateMobile($empid, $mobile) {
        $sql = "UPDATE " . self::$table_name . " SET mobile ='$mobile' WHERE bm_empid = '$empid' ";

        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updatedob($empid, $dob) {
        $sql = "UPDATE " . self::$table_name . " SET DOB ='$dob' WHERE bm_empid = '$empid' ";

        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updatedoa($empid, $doa) {
        $sql = "UPDATE " . self::$table_name . " SET doa ='$doa' WHERE bm_empid = '$empid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updatepic($empid, $path) {
        $sql = "UPDATE " . self::$table_name . " SET profile_photo ='$path' WHERE bm_empid = '$empid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>