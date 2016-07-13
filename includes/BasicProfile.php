<?php require_once('database.php'); ?>
<?php

class BasicProfile {

    protected static $table_name = "doc_basic_profile";
    public static
            $db_fields = array(
        'docid', 'name', 'empid', 'id', 'DOB', 'DOA', 'class', 'clinic_address',
        'residential_address', 'receive_mailers', 'receive_sms', 'yrs_of_practice',
        'type', 'behaviour', 'inclination_to_speaker', 'potential_to_speaker',
        'hobbies', 'activity_inclination', 'gen_ophthal', 'retina', 'glaucoma', 'cornea',
        'other', 'daily_opd', 'value_per_rx', 'pharma_potential', 'month', 'total', 'msl_code', 'clinic_name', 'any_other',
        'plot1', 'street1', 'area1', 'city1', 'state1', 'pincode1',
        'plot2', 'street2', 'area2', 'city2', 'state2', 'pincode2', 'value_per_month'
    );
    public $docid;
    public $name;
    public $empid;
    public $id;
    public $DOB;
    public $DOA;
    public $class;
    public $clinic_address;
    public $residential_address;
    public $receive_mailers;
    public $receive_sms;
    public $yrs_of_practice;
    public $type;
    public $behaviour = " ";
    public $inclination_to_speaker;
    public $potential_to_speaker;
    public $hobbies = " ";
    public $activity_inclination = " ";
    public $gen_ophthal;
    public $retina;
    public $glaucoma;
    public $cornea;
    public $other;
    public $daily_opd = " ";
    public $value_per_rx = " ";
    public $pharma_potential = " ";
    public $month = 0;
    public $total;
    public $msl_code;
    public $clinic_name;
    public $any_other;
    public $plot1;
    public $street1;
    public $area1;
    public $city1;
    public $state1;
    public $pincode1;
    public $plot2;
    public $street2;
    public $area2;
    public $city2;
    public $state2;
    public $pincode2;
    public $value_per_month;

    // Common Database Methods
    public static function find_all($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid='$empid' ");
    }

    public static function find_by_id($id = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE id='$id' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_docid($docid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE docid={$docid} LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function findBirthDate($docid = "") {
        $result_array = self::find_by_sql("SELECT DOB FROM " . self::$table_name . " WHERE docid={$docid} LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_DOB() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(DOB) = month(curdate())
            AND day(DOB) = day(curdate()) ");
    }

    public static function find_by_DOA() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE month(DOA) = month(curdate())
            AND day(DOA) = day(curdate()) ");
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
                    . " DOB <= DATE_ADD(DATE_SUB(CURDATE(), INTERVAL YEAR(CURDATE()) - YEAR(DOB) YEAR), INTERVAL 7 DAY ))";

            return self::find_by_sql($sql);
        }
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

    public static function count_all($empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_basic_profile db INNER JOIN doctors d ON d.docid = db.docid WHERE d.is_delete = 0 AND db.empid='$empid' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
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
// sanitize the values before submitting
// Note: does not alter the actual value of each attribute
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

//public function save() {
// A new record won't have an id yet.
//return isset($this->id) ? $this->update() : $this->create();
//    }

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

    public function update($docid) {
        global $database;

// - single-quotes around all values
// - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE docid={$docid}";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function delete() {
        global $database;

        $sql = "DELETE FROM " . self::$table_name;
        $sql .= " WHERE docid=" . $database->escape_value($this->docid);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function autoGenerate_id() {

        $num = self::count_all();
        ++$num; // add 1;
        return $num;
    }

    public static function drawPieChart($empid, $class) {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE empid='$empid' AND class='$class'";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function SMdrawPieChart($sm_empid, $class) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `doc_basic_profile` WHERE empid IN (
        SELECT empid FROM employees WHERE bm_empid IN (SELECT bm_empid FROM bm WHERE sm_empid ='{$sm_empid}')
       ) AND CLASS='$class' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function adminDrawPieChart($class) {
        $sql = "SELECT COUNT(*) FROM `doc_basic_profile` WHERE  CLASS='$class' ";
        return self::returnCount($sql);
    }

    public static function returnCount($sql) {
        global $database;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function BMdrawPieChart($bm_empid, $class) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `doc_basic_profile` WHERE empid IN (
        SELECT empid FROM employees WHERE bm_empid ='{$bm_empid}'
      ) AND CLASS='$class' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
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

    public static function view_basic_profile($finalBasicProfie) {
        $output = '';
        if (!empty($finalBasicProfie)){
        $address1 = array($finalBasicProfie->plot1, $finalBasicProfie->street1, $finalBasicProfie->area1, $finalBasicProfie->city1, $finalBasicProfie->state1, $finalBasicProfie->pincode1);
        $address2 = array($finalBasicProfie->plot2, $finalBasicProfie->street2, $finalBasicProfie->area2, $finalBasicProfie->city2, $finalBasicProfie->state2, $finalBasicProfie->pincode2);
        $trimmed_array1 = array_filter(array_map('trim', $address1));
        $trimmed_array1 = join(",<br/>", $trimmed_array1);
        $trimmed_array2 = array_filter(array_map('trim', $address2));
        $trimmed_array2 = join(",<br/>", $trimmed_array2);
        $date1 = ($finalBasicProfie->DOB == '0000-00-00') ? '' : date('d-m-Y', strtotime($finalBasicProfie->DOB));
        $date2 = ($finalBasicProfie->DOA == '0000-00-00') ? '' : date('d-m-Y', strtotime($finalBasicProfie->DOA));
        $output .= '<table cellspacing="0" class="table table-bordered " style="margin-top: 1em">

            <tr>
                <td>Date of Birth:</td>
                <td>
                    ' . $date1 . '
                </td>
            </tr>
            <tr>
                <td>Date Of Anniversary:</td>
                <td>
                    ' . $date2 . '
                </td>
            </tr>

            <tr>
                <td>Class:</td>
                <td>
                    ' . $finalBasicProfie->class . '
                </td>
            </tr>
            <tr>
                <td>Clinic Name:</td>
                <td>
                    ' . $finalBasicProfie->clinic_name . '
                </td>
            </tr>
            <tr>
                <td>Want to receive mailers</td>
                <td>
                    ' . $finalBasicProfie->receive_mailers . '
                </td>

            </tr>
            <tr>
                <td>Want to receive SMS</td>
                <td>
                    ' . $finalBasicProfie->receive_sms . '
                </td>

            </tr>

            <tr>
                <td>Years Of Practice:</td>
                <td>
                    ' . $finalBasicProfie->yrs_of_practice . '
                </td>
            </tr>

            <tr>
                <td>Type of Doctor:</td>
                <td> 
                    ' . $finalBasicProfie->type . '
                </td>
            </tr>

            <tr>
                <td>Behaviour of Doctor:</td>
                <td> 
                    ' . $finalBasicProfie->behaviour . '
                </td>
            </tr>

            <tr>
                <td>Inclination To Speaker:</td>
                <td>
                    ' . $finalBasicProfie->inclination_to_speaker . '
                </td>

            </tr>
            <tr>
                <td>Potential To Speaker:</td>
                <td>
                    ' . $finalBasicProfie->potential_to_speaker . '
                </td>

            </tr>


            <tr>
                <td>Clinic Address:</td>
                <td>
                    ' . $trimmed_array1 . '
                </td>
            </tr>

            <tr>
                <td>Residential Address:</td>
                <td>
                    ' . $trimmed_array2 . '
                </td>
            </tr>

            <tr>
                <td>Hobbies And Interest:</td>
                <td>
                    ' . $finalBasicProfie->hobbies . '
        </td>
        </tr>



        <tr>
        <td>Activity Inclination:</td>
        <td>
        ' . $finalBasicProfie->activity_inclination . '</td>
            </tr>
            </tr>

            <tr><th colspan="2">Type Of Practice</th>

            <tr>
                <td>Gen Opthal:</td>
                <td>
                    ' . $finalBasicProfie->gen_ophthal . " %" . '
        </td>
        </tr>
        <tr>
        <td>Retina:</td>
        <td>
        ' . $finalBasicProfie->retina . " %" . '
                </td>
            </tr>
            <tr>
                <td>Glaucoma:</td>
                <td>
                    ' . $finalBasicProfie->glaucoma . " %" . '
        </td>
        </tr>
        <tr>
        <td>Cornea:</td>
        <td>
        ' . $finalBasicProfie->cornea . " %" . '
                </td>
            </tr>
            <tr>
                <td>' . $finalBasicProfie->any_other . ' </td>
        <td>
        ' . $finalBasicProfie->other . " %" . '
                </td>
            </tr>
            </td>
            </tr>
            <tr>
                <th>Average Daily OPD (No. of Patients) </th>
                <td>
                   ' . $finalBasicProfie->daily_opd . '
        </td>
        </tr>
        <tr>
        <th>Average Value Per Rx (No. in Rs.)</th>
        <td>
        ' . $finalBasicProfie->value_per_rx . '
                </td>
            </tr>
            <tr>
        <th>Average surgery Per Month </th>
        <td>
        ' . $finalBasicProfie->value_per_month . '
                </td>
            </tr>
            <tr>
                <th>Pharma Potential (No. in Rs.)</th>
                <td>
                    ' . $finalBasicProfie->pharma_potential . '
        </td>
        </tr>
        <tr>
        <th>MSL Code:</th>
        <td>
        ' . $finalBasicProfie->msl_code . '
                </td>
            </tr>
        </table>';

        }
        return $output;
    }

}

?>