<?php

require_once('database.php');

class Competitors {

    protected static $table_name = "competitors";
    protected static $db_fields = array('docid', 'name', 'empid', 'cipla', 'company1', 'company2', 'company3', 'company4', 'company5', 'company6', 'company7');
    public $docid;
    public $name;
    public $empid;
    public $cipla = "";
    public $company1 = "";
    public $company2 = "";
    public $company3 = "";
    public $company4 = "";
    public $company5 = "";
    public $company6 = "";
    public $company7 = "";

    // Common Database Methods
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_docid($docid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE docid='$docid'  ");
        return !empty($result_array) ? array_shift($result_array) : false;
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
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE empid='$empid'";
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
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE docid='$docid' ";
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

    public static function drawBarGraph($empid, $total) {
        global $database;
        $sql = "SELECT COUNT(*)  FROM " . self::$table_name . " WHERE empid='$empid' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function SMdrawBarGraph($sm_empid, $total) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `doc_buisness_profile` WHERE empid IN (
    SELECT empid FROM employees WHERE bm_empid IN (SELECT bm_empid FROM bm WHERE sm_empid ='{$sm_empid}')
      ) AND  $total AND MONTH(month) = MONTH(CURDATE()) ";

        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function BMdrawBarGraph($bm_empid, $total) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `doc_buisness_profile` WHERE empid IN (
    SELECT empid FROM employees WHERE bm_empid ='{$bm_empid}'
  ) AND $total AND MONTH(month) = MONTH(CURDATE()) ";

        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function drawBrandGraph($brandName, $month, $empid) {
        global $database;
        $sql = "SELECT SUM(" . $brandName . ") AS SUM FROM " . self::$table_name . " WHERE month(month) = {$month} AND empid = '{$empid}' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['SUM'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function drawBrandGraph2($doctorName, $brandName) {
        global $database;
        $sql = "SELECT $brandName FROM " . self::$table_name . " WHERE month(month) = month(CURDATE()) AND name = '{$doctorName}' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row[0])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function drawBrandGraph3($doctorName, $brandName, $month) {
        global $database;
        $sql = "SELECT SUM(" . $brandName . ") AS SUM FROM " . self::$table_name . " WHERE month(month) = '{$month}' AND name = '{$doctorName}' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['SUM'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function BMdrawBrandGraph($brandName, $month, $bm_empid) {
        global $database;
        $sql = "SELECT SUM(" . $brandName . ") AS SUM FROM " . self::$table_name . " WHERE month(month) = '{$month}' AND empid IN (
            SELECT empid FROM employees WHERE bm_empid = '{$bm_empid}'
    )";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    /* public static function BMdrawBrandGraph2($doctorName,$brandName){
      global $database;
      $sql="SELECT $brandName FROM ".self::$table_name." WHERE month(month) = month(CURDATE()) AND name = '{$doctorName}' ";
      $result_set = $database->query($sql);
      $row = $database->fetch_array($result_set);
      return array_shift($row);
      } */

    public static function BMdrawBrandGraph3($doctorName, $brandName, $month) {
        global $database;
        $sql = "SELECT  SUM(" . $brandName . ")FROM " . self::$table_name . " WHERE month(month) = '{$month}' AND name = '{$doctorName}' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function SMdrawBrandGraph($brandName, $month, $sm_empid) {
        global $database;
        $sql = "SELECT SUM(" . $brandName . ") FROM " . self::$table_name . " WHERE month(month) = '{$month}' AND empid IN (
            SELECT empid FROM employees WHERE bm_empid IN (SELECT bm_empid FROM bm WHERE sm_empid = '{$sm_empid}')
    )";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function viewProfile($docid) {
        $output ='';
        $competitor = Competitors::find_by_docid($docid);

        if (!empty($competitor)) {
            $BusiProfile = BusiProfile::docwise_business($docid, $_SESSION['_CURRENT_MONTH']);
            $output = '<table cellspacing="0" class="table table-bordered">
                                <thead><tr>
                                    <th>ALLERGAN</th>
                                    <th>SUN</th>
                                    <th>ALCON</th>
                                    <th>AJANTA</th>
                                    <th>MICRO LAB</th>
                                    <th>FDC</th>
                                    <th>INTAS</th>
                                    <th>CIPLA</th>
                                </tr></thead> ';

            $output .= isset($competitor->company1) ? "<tr><td data-title='ALLERGAN'>" . $competitor->company1 . "&nbsp;</td>" : "<tr><td data-title='ALLERGAN'>-</td>";
            $output .=  isset($competitor->company2) ? "<td data-title='SUN'>" . $competitor->company2 . "&nbsp;</td>" : "<td data-title='SUN'>-</td>";
            $output .=  isset($competitor->company3) ? "<td data-title='ALCON'>" . $competitor->company3 . "&nbsp;</td>" : "<td data-title='ALCON'>-</td>";
            $output .=  isset($competitor->company4) ? "<td data-title='AJANTA'>" . $competitor->company4 . "&nbsp;</td>" : "<td data-title='AJANTA'>-</td>";
            $output .=  isset($competitor->company5) ? "<td data-title='MICRO LAB'>" . $competitor->company5 . "&nbsp;</td>" : "<td data-title='MICRO LAB'>-</td>";
            $output .=  isset($competitor->company6) ? "<td data-title='FDC'>" . $competitor->company6 . "&nbsp;</td>" : "<td data-title='FDC'>-</td>";
            $output .=  isset($competitor->company7) ? "<td data-title='INTAS'>" . $competitor->company7 . "&nbsp;</td>" : "<td data-title='INTAS'>-</td>";
            $output .=  isset($BusiProfile) ? "<td data-title='CIPLA'>" . $BusiProfile . "&nbsp;</td></tr>" : "<td data-title='CIPLA'>-</td></tr>";
            $output .= '</table>';
        }  else {
            $output ='Details Not Found';
        }
        
        return $output;
    }

}

?>