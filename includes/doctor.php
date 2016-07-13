<?php

require_once('database.php');

class Doctor extends QueryWrapper {

    protected static $table_name = "doctors";
    protected static $db_fields = array('docid', 'empid', 'emailid', 'name', 'mobile', 'area', 'speciality', 'is_delete');
    public $docid;
    public $empid;
    public $emailid;
    public $name;
    public $mobile;
    public $area;
    public $speciality;
    public $is_delete;

    // Common Database Methods

    public static function find() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE is_delete = 0 ");
    }

    public static function find_all($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid='$empid' AND is_delete = 0 ");
    }

    //For replacing employee ids............
    public static function find_by_id($empid) {
        global $database;
        $result = $database->query("SELECT * FROM " . self::$table_name . " WHERE empid='$empid' AND is_delete = 0");
        return $result;
    }

    public static function find_by_empname($empname) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid IN (SELECT empid FROM employees Where name ='{$empname}') AND is_delete = 0 ");
    }

    public static function find_all_doctors($bm_empid) {
        global $database;
        $sql = "SELECT * FROM doctors WHERE empid IN( SELECT empid from employees Where bm_empid='{$bm_empid}' ) AND is_delete = 0  ";
        return self::find_by_sql($sql);
    }

    public static function find_doctor_by_area($area, $empid) {
        return QueryWrapper::executeQuery("SELECT * FROM " . self::$table_name . " WHERE empid='$empid' AND area = '$area' AND is_delete = 0 ");
    }

    public static function areaList($empid) {
        $areaList = array();
        $result = QueryWrapper::executeQuery("SELECT DISTINCT(area) FROM " . self::$table_name . " WHERE empid='$empid' AND is_delete = 0 ");
        if (!empty($result)) {
            foreach ($result as $areaname) {
                array_push($areaList, $areaname->area);
            }
        }
        return json_encode($areaList);
    }

    public static function find_all_BMdoctors($sm_empid) {
        global $database;
        $sql = "SELECT * FROM doctors WHERE empid IN( SELECT empid from employees Where bm_empid IN(
      			SELECT bm_empid FROM bm WHERE sm_empid='{$sm_empid}'
      	) ) AND is_delete = 0 ";
        return self::find_by_sql($sql);
    }

    public static function DoctorBirthdays($empid) {
        $sql = "SELECT * FROM doctors WHERE docid IN (SELECT docid FROM doc_basic_profile WHERE empid='{$empid}') AND is_delete = 0 ";
        return self::find_by_sql($sql);
    }

    public static function SearchName($name, $empid) {
        $names = array();
        $sql = "SELECT docid , name FROM doctors WHERE name LIKE '%$name%' AND empid='{$empid}' AND is_delete = 0 ";
        global $database;
        $result = $database->query($sql);
        while ($row = $database->fetch_array($result)) {
            echo "<li value = '" . $row['docid'] . "' >" . $row['name'] . "</li>";
        }
    }

    public static function NextWeekBirthdays($empid = '') {
        $date = strtotime("+7 day");
        $month = date('m ', $date);
        $day = date('d', $date);
        $next_seven = date('d', strtotime("+14 day"));

        if ($empid != '') {
            $sql = "  SELECT * FROM doc_basic_profile "
                    . " WHERE "
                    . " month(DOB) = '$month' AND day(DOB) BETWEEN '$day' AND '$next_seven' AND empid='{$empid}' ";
            //echo $sql;
            return QueryWrapper::executeQuery($sql);
        } else {
            $sql = "select * from doctors where docid IN(SELECT docid FROM doc_basic_profile\n"
                    . " WHERE \n"
                    . " DOB >= DATE_SUB(CURDATE(), INTERVAL YEAR(CURDATE()) - YEAR(DOB) YEAR)\n"
                    . " AND\n"
                    . " DOB <= DATE_ADD(DATE_SUB(CURDATE(), INTERVAL YEAR(CURDATE()) - YEAR(DOB) YEAR), INTERVAL 7 DAY )) AND is_delete = 0 ";

            return self::find_by_sql($sql);
        }
    }

    public static function showBirthDayFlash($empid) {
        $Birthdays = Doctor::NextWeekBirthdays($empid);
        $output = '';
        if (isset($Birthdays) && !empty($Birthdays)) {
            $output = '<marquee style = "margin-top : 7% ; font-size : 16px"><span style = "font-weight: bold;color:red;font-size:20px">Upcoming Birthdays : </span>';

            foreach ($Birthdays as $doctor) {
                $date = explode("-", $doctor->DOB);
                $output .= "<b>" . $doctor->name . "</b> : " . $date[2] . "-" . $date[1] . "-" . $date[0] . " , ";
            }
        }
        $output .= '</marquee>';
        return $output;
    }

    public static function NextMonthBirthdays($empid) {
        if ($empid != '') {
            $sql = "select * from doctors where docid IN (SELECT docid FROM doc_basic_profile WHERE IF\n"
                    . " ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 1,\n"
                    . " MONTH(DOB) = 1) AND empid='{$empid}') ";

            return self::find_by_sql($sql);
        } else {
            $sql = "select * from doctors where docid IN (SELECT docid FROM doc_basic_profile WHERE IF\n"
                    . " ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 1,\n"
                    . " MONTH(DOB) = 1)) ";
            return self::find_by_sql($sql);
        }
    }

    public static function Next3MonthBirthdays($empid) {
        if ($empid != '') {
            $sql = "Select * from doctors where docid IN( 
          SELECT docid FROM doc_basic_profile WHERE
          IF ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 1, MONTH( DOB ) = 1)
          OR IF ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 2, MONTH( DOB ) = 1) 
          OR IF ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 3, MONTH( DOB ) = 1)) AND empid='{$empid}' ";

            return self::find_by_sql($sql);
        } else {
            $sql = "Select * from doctors where docid IN( 
          SELECT docid FROM doc_basic_profile WHERE
          IF ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 1, MONTH( DOB ) = 1)
          OR IF ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 2, MONTH( DOB ) = 1) 
          OR IF ( MONTH( NOW() ) < 12, MONTH( DOB ) = MONTH( NOW() ) + 3, MONTH( DOB ) = 1)) ";
            return self::find_by_sql($sql);
        }
    }

    public static function Search($empid, $name) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid='$empid' AND name like '%$name%' AND is_delete = 0 ");
    }

    public static function find_by_docid($docid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE docid={$docid} AND is_delete = 0 LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_sql($sql = "") {
        global $database;
        //echo $sql;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = self::instantiate($row);
        }
        
        return $object_array;
    }

    public static function count_all($empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE empid='$empid' AND is_delete = 0 ";
        return self::returnCount($sql);
    }

    public static function all() {
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE is_delete = 0 AND empid != '' ";
        return self::returnCount($sql);
    }

    public static function pagination($limit, $offset) {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE is_delete=0 LIMIT $limit OFFSET $offset";
        return self::find_by_sql($sql);
    }

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

        // - single-quotes around all values
        // - escape all values to prevent SQL injection

        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . self::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";

        //echo $sql;
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
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE docid=" . $database->escape_value($this->docid);
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function delete() {
        global $database;
        $this->changeDeleteStatus();
        $table_names = array('doctors', 'doc_buisness_profile', 'daily_call_planning', 'doctor_visit');
        foreach ($table_names as $table) {
            $sql = "DELETE FROM " . $table . " WHERE docid = '$this->docid' ";
            $result = $database->query($sql);
            //var_dump($result);
        }

        return TRUE;
    }

    public function changeDeleteStatus() {
        global $database;
        $sql = "Update doctors SET is_delete = 1 WHERE docid = '$this->docid' ";
        $database->query($sql);
    }

    public static function empwisebasicCount($empid) {
        $finalCount = 0;
        $Doctors = self::find_all($empid);
        foreach ($Doctors as $Doctor) {
            $count = self::count_basic_profile($Doctor->docid);
            $finalCount += $count == 1 ? 1 : 0;
        }
        return $finalCount;
    }

    public static function basiccount($empid) {
        $sql = "SELECT 
                COUNT(db.`docid`) AS basic

              FROM `employees` e 
              LEFT JOIN doctors d 
              ON d.`empid` = e.`empid`
                LEFT JOIN `doc_basic_profile` db 
                  ON d.`docid` = db.`docid` 

              WHERE d.is_delete = 0 
                AND db.`activity_inclination` != '' 
                AND db.`area1` != '' 
                AND db.`area2` != '' 
                AND db.`behaviour` != '' 
                AND db.`class` != '' 
                AND db.`clinic_name` != '' 
                AND db.`cornea` != '' 
                AND db.`daily_opd` != '' 
                AND db.`DOB` != '0000-00-00' 
                AND db.`gen_ophthal` != '' 
                AND db.`glaucoma` != '' 
                AND db.`hobbies` != '' 
                AND db.`inclination_to_speaker` != '' 
                AND db.`msl_code` != '' 
                AND db.`pharma_potential` != '' 
                AND db.`potential_to_speaker` != '' 
                AND db.`receive_mailers` != '' 
                AND db.`receive_sms` != '' 
                AND db.`retina` != '' 
                AND db.`state1` != '' 
                AND db.`state2` != '' 
                AND db.`total` != '' 
                AND db.`type` != '' 
                AND db.`value_per_rx` != '' 
                AND db.`yrs_of_practice` != '' 
                AND db.`city1` != '' 
                AND db.`city2` != '' 
                AND d.`empid` = {$empid}";
        $result_array = QueryWrapper::executeQuery($sql);
        $result_array = array_shift($result_array);
        return $result_array->basic;
    }

    public static function serviceCount($empid) {
        $finalCount = 0;
        $Doctors = self::find_all($empid);
        foreach ($Doctors as $Doctor) {
            $count = self::count_service($Doctor->docid);
            $finalCount += $count == 1 ? 1 : 0;
        }
        return $finalCount;
    }

    public static function Countcomp($empid) {
        $finalCount = 0;
        $Doctors = self::find_all($empid);
        foreach ($Doctors as $Doctor) {
            $count = self::count_competitor($Doctor->docid);
            $finalCount += $count == 1 ? 1 : 0;
        }
        return $finalCount;
    }

    public static function empwiseacademicCount($empid) {
        $finalCount = 0;
        $Doctors = self::find_all($empid);
        foreach ($Doctors as $Doctor) {
            $count = self::count_academic_profile($Doctor->docid);
            $finalCount += $count == 1 ? 1 : 0;
        }
        return $finalCount;
    }

    public static function count_basic_profile($docid) {
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

    public static function count_buisness_profile($docid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_buisness_profile WHERE docid ={$docid} AND MONTH(month) = {$_SESSION['_CURRENT_MONTH']} ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        $count = array_shift($row);
        return $count === 0 ? 0 : 1;
    }

    public static function count_competitor($docid) {
        $sql = "SELECT COUNT(*) FROM competitors WHERE docid ={$docid}";
        return self::returnCount($sql);
    }

    public static function count_service($docid) {
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

    public static function count_academic_profile($docid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_academic_profile WHERE docid ={$docid} ";
        return self::returnCount($sql);
    }

    public static function totalProfileCount($docid) {
        $id = $docid;
        $basicCount = Doctor:: count_basic_profile($id);
        //$buisnessCount = Doctor:: count_buisness_profile($id);
        $academicCount = Doctor:: count_academic_profile($id);
        $serviceCount = Doctor:: count_service($id);
        $competitorCount = Doctor::count_competitor($id);
        //return $basicCount + $buisnessCount + $academicCount + $serviceCount + $competitorCount;
        return $basicCount + $academicCount + $serviceCount + $competitorCount;
    }

    public static function profile_complete_percentage($docid) {
        $count = self::totalProfileCount($docid);
        //echo $count;
        return ($count / 4 * 100 );
    }

    public static function profileCount_empwise($empid) {
        $finalCount = 0;
        $Doctors = self::find_all($empid);
        foreach ($Doctors as $Doctor) {
            $count = self::totalProfileCount($Doctor->docid);
            $finalCount += $count == 4 ? 1 : 0;
        }
        return $finalCount;
    }

    public static function completed($empid) {
        $sql = "SELECT 
                COUNT(d.`docid`) AS completed 

            FROM `employees` e 
            LEFT JOIN doctors d 
            ON d.`empid` = e.`empid`
              INNER JOIN `doc_basic_profile` db 
                ON d.`docid` = db.`docid` 
              INNER JOIN services ser 
                ON d.`docid` = ser.`docid` 
              INNER JOIN `doc_academic_profile` da 
                ON d.`docid` = da.`docid` 
              INNER JOIN competitors cmpt 
                ON d.`docid` = cmpt.`docid` 
            WHERE d.is_delete = 0 
              AND db.`activity_inclination` != '' 
              AND db.`area1` != '' 
              AND db.`area2` != '' 
              AND db.`behaviour` != '' 
              AND db.`class` != '' 
              AND db.`clinic_name` != '' 
              AND db.`cornea` != '' 
              AND db.`daily_opd` != '' 
              AND db.`DOB` != '0000-00-00' 
              AND db.`gen_ophthal` != '' 
              AND db.`glaucoma` != '' 
              AND db.`hobbies` != '' 
              AND db.`inclination_to_speaker` != '' 
              AND db.`msl_code` != '' 
              AND db.`pharma_potential` != '' 
              AND db.`potential_to_speaker` != '' 
              AND db.`receive_mailers` != '' 
              AND db.`receive_sms` != '' 
              AND db.`retina` != '' 
              AND db.`state1` != '' 
              AND db.`state2` != '' 
              AND db.`total` != '' 
              AND db.`type` != '' 
              AND db.`value_per_rx` != '' 
              AND db.`yrs_of_practice` != '' 
              AND db.`city1` != '' 
              AND db.`city2` != '' 
              AND d.`empid` = {$empid} ";
        $result_array = QueryWrapper::executeQuery($sql);
        $result_array = array_shift($result_array);
        return $result_array->completed;
    }

    public static function allProfileCount($empid) {
        $sql = " SELECT 
                   COUNT(d.docid) AS DOCTOR_COUNT, COUNT(da.`docid`) AS academic , COUNT(ser.`docid`) service , COUNT(`cmpt`.`docid`) compt,
                   COUNT(db.`docid`) AS basic,SUM(CASE WHEN d.docid IS NOT NULL AND db.`docid` IS NOT NULL AND da.`docid` IS NOT NULL AND ser.`docid` IS NOT NULL AND `cmpt`.`docid` IS NOT NULL THEN 1 ELSE 0 END) AS completed
                   FROM `employees` e 
                   LEFT JOIN doctors d 
                   ON d.`empid` = e.`empid`
                       LEFT JOIN services ser 
                       ON d.`docid` = ser.`docid` 
                     LEFT JOIN `doc_academic_profile` da 
                       ON d.`docid` = da.`docid` 
                     LEFT JOIN competitors cmpt 
                       ON d.`docid` = cmpt.`docid` 
                       LEFT JOIN `doc_basic_profile` db 
                    ON d.`docid` = db.`docid` 
     AND db.`activity_inclination` != '' 
                AND db.`area1` != '' 
                AND db.`area2` != '' 
                AND db.`behaviour` != '' 
                AND db.`class` != '' 
                AND db.`clinic_name` != '' 
                AND db.`cornea` != '' 
                AND db.`daily_opd` != '' 
                AND db.`DOB` != '0000-00-00' 
                AND db.`gen_ophthal` != '' 
                AND db.`glaucoma` != '' 
                AND db.`hobbies` != '' 
                AND db.`inclination_to_speaker` != '' 
                AND db.`msl_code` != '' 
                AND db.`pharma_potential` != '' 
                AND db.`potential_to_speaker` != '' 
                AND db.`receive_mailers` != '' 
                AND db.`receive_sms` != '' 
                AND db.`retina` != '' 
                AND db.`state1` != '' 
                AND db.`state2` != '' 
                AND db.`total` != '' 
                AND db.`type` != '' 
                AND db.`value_per_rx` != '' 
                AND db.`yrs_of_practice` != '' 
                AND db.`city1` != '' 
                AND db.`city2` != '' 
                       WHERE d.is_delete = 0 AND d.`empid` = {$empid}";
        $result_array = QueryWrapper::executeQuery($sql);
        return !empty($result_array) ? array_shift($result_array) : FALSE;
    }
    
    public static function doctorListAdmnin(){
        $sql = "SELECT 
                   d.`name`,d.emailid,d.speciality,d.area,d.docid,db.`msl_code`,db.class,
                   ( (COUNT(da.docid) + COUNT(ser.docid) + COUNT(cmpt.docid) + COUNT(db.docid)) / 4 * 100) AS percent
                   FROM `employees` e 
                   LEFT JOIN doctors d 
                   ON d.`empid` = e.`empid`
                       LEFT JOIN services ser 
                       ON d.`docid` = ser.`docid` 
                     LEFT JOIN `doc_academic_profile` da 
                       ON d.`docid` = da.`docid` 
                     LEFT JOIN competitors cmpt 
                       ON d.`docid` = cmpt.`docid` 
                       LEFT JOIN `doc_basic_profile` db 
                    ON d.`docid` = db.`docid` 
     AND db.`activity_inclination` != '' 
                AND db.`area1` != '' 
                AND db.`area2` != '' 
                AND db.`behaviour` != '' 
                AND db.`class` != '' 
                AND db.`clinic_name` != '' 
                AND db.`cornea` != '' 
                AND db.`daily_opd` != '' 
                AND db.`DOB` != '0000-00-00' 
                AND db.`gen_ophthal` != '' 
                AND db.`glaucoma` != '' 
                AND db.`hobbies` != '' 
                AND db.`inclination_to_speaker` != '' 
                AND db.`msl_code` != '' 
                AND db.`pharma_potential` != '' 
                AND db.`potential_to_speaker` != '' 
                AND db.`receive_mailers` != '' 
                AND db.`receive_sms` != '' 
                AND db.`retina` != '' 
                AND db.`state1` != '' 
                AND db.`state2` != '' 
                AND db.`total` != '' 
                AND db.`type` != '' 
                AND db.`value_per_rx` != '' 
                AND db.`yrs_of_practice` != '' 
                AND db.`city1` != '' 
                AND db.`city2` != '' 
                       WHERE d.is_delete = 0 GROUP BY d.docid  ";
        return QueryWrapper::executeQuery($sql);
    }

    public static function profileCount_admin() {
        $finalCount = 0;
        //$Doctors = self::find();
        $sql = "SELECT COUNT(d.`docid`) AS doctor_count FROM doctors d 
                INNER JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` 
                INNER JOIN services ser ON d.`docid` = ser.`docid` 
                INNER JOIN `doc_academic_profile` da ON d.`docid` = da.`docid` 
                INNER JOIN competitors cmpt ON d.`docid` = cmpt.`docid` 
                WHERE d.is_delete = 0 AND db.`activity_inclination` != '' AND 
                db.`area1`!= '' AND db.`area2`!= '' AND db.`behaviour`!= '' AND db.`class`!= '' AND 
                db.`clinic_name`!= '' AND db.`cornea`!= '' AND db.`daily_opd`!= ''  AND 
                db.`DOB`!= '0000-00-00' AND db.`gen_ophthal`!= '' AND db.`glaucoma`!= '' AND 
                db.`hobbies`!= '' AND db.`inclination_to_speaker`!= '' AND 
                db.`msl_code`!= '' AND db.`pharma_potential`!= ''  AND 
                db.`potential_to_speaker`!= '' AND db.`receive_mailers`!= '' AND 
                db.`receive_sms`!= '' AND db.`retina`!= '' AND db.`state1`!= '' AND db.`state2`!= ''  AND db.`total`!= '' AND db.`type`!= '' AND 
                db.`value_per_month`!= '' AND db.`value_per_rx`!= 0 AND db.`yrs_of_practice` != '' AND db.`city1` != '' AND db.`city2` !=''  ";

        $result_array = QueryWrapper::executeQuery($sql);
        $result_array = array_shift($result_array);
        return $result_array->doctor_count;
    }

    public static function profileCount_bmwise($bm_empid) {
        $finalCount = 0;
        $Employees = Employee::find_by_bmid($bm_empid);
        if ($Employees) {
            foreach ($Employees as $Employee) {
                $count = self::profileCount_empwise($Employee->empid);
                $finalCount += $count;
            }
        }

        return $finalCount;
    }

    public static function profileCount_smwise($sm_empid) {
        $finalCount = 0;
        $Employees = Employee::find_by_smid($sm_empid);
        if ($Employees) {
            foreach ($Employees as $Employee) {
                $count = self::profileCount_empwise($Employee->empid);
                $finalCount += $count;
            }
        }

        return $finalCount;
    }

    public static function returnCount($sql) {
        global $database;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function updateMobile($docid, $mobile) {
        $sql = "UPDATE " . self::$table_name . " SET mobile ='$mobile' WHERE docid = '$docid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updateEmail($docid, $email) {
        $sql = "UPDATE " . self::$table_name . " SET emailid ='$email' WHERE docid = '$docid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function updateArea($docid, $area) {
        $sql = "UPDATE " . self::$table_name . " SET area ='$area' WHERE docid = '$docid' ";
        global $database;
        $result = $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function buildQuery($conditions, $paging = "") {
        $sql = "SELECT 
                   d.`name`,d.emailid,d.speciality,d.area,d.docid,d.mobile,db.`msl_code`,db.class,
                   TRUNCATE( (COUNT(da.docid) + COUNT(ser.docid) + COUNT(cmpt.docid) + COUNT(db.docid)) / 4 * 100 , 0) AS percent 
                   FROM `employees` e 
                   INNER JOIN doctors d 
                   ON d.`empid` = e.`empid`
                       LEFT JOIN services ser 
                       ON d.`docid` = ser.`docid` 
                       
                     LEFT JOIN `doc_academic_profile` da 
                       ON d.`docid` = da.`docid` 
                     LEFT JOIN competitors cmpt 
                       ON d.`docid` = cmpt.`docid` 
                       LEFT JOIN `doc_basic_profile` db 
                    ON d.`docid` = db.`docid` 
                    AND  d.is_delete = 0
                AND db.`activity_inclination` != '' 
                AND db.`area1` != '' 
                AND db.`area2` != '' 
                AND db.`behaviour` != '' 
                AND db.`class` != '' 
                AND db.`clinic_name` != '' 
                AND db.`cornea` != '' 
                AND db.`daily_opd` != '' 
                AND db.`DOB` != '0000-00-00' 
                AND db.`gen_ophthal` != '' 
                AND db.`glaucoma` != '' 
                AND db.`hobbies` != '' 
                AND db.`inclination_to_speaker` != '' 
                AND db.`msl_code` != '' 
                AND db.`pharma_potential` != '' 
                AND db.`potential_to_speaker` != '' 
                AND db.`receive_mailers` != '' 
                AND db.`receive_sms` != '' 
                AND db.`retina` != '' 
                AND db.`state1` != '' 
                AND db.`state2` != '' 
                AND db.`total` != '' 
                AND db.`type` != '' 
                AND db.`value_per_rx` != '' 
                AND db.`yrs_of_practice` != '' 
                AND db.`city1` != '' 
                AND db.`city2` != '' ";
        if (!empty($conditions)) {
            $sql .= join(" ", $conditions);
        }
        $sql .=" GROUP BY d.`docid` ";
        if ($paging != '') {
            $sql .= $paging;
        }
        //echo '<pre>'.$sql .'</pre>';
        return QueryWrapper::executeQuery($sql);
    }

}

?>