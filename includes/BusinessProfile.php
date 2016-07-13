<?php

date_default_timezone_set("Asia/Kolkata");
require_once('database.php');
$__CURRENTMONTH = $_SESSION['_CURRENT_MONTH'];

class BusiProfile {
    
    protected static $table_name = "doc_buisness_profile";
    protected static $db_fields = array('id', 'docid', 'name', 'empid', 'month', 'brand1', 'brand_id', 'total', 'created');
    public $id;
    public $docid;
    public $name;
    public $empid;
    public $month;
    public $brand1;
    public $brand_id;
    public $total;
    public $created;

    public static function SUM1($empid) {
        global $database;
        $sql = "SELECT SUM(" . $empid . ") FROM " . self::$table_name . " WHERE month(month)={$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016'  ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    // Common Database Methods
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function collect_id($empid) {
        return self::find_by_sql("SELECT id FROM " . self::$table_name . " WHERE empid = '$empid' ");
    }

    public static function find_by_docid($docid = "") {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE docid={$docid} AND month(month) ={$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016'  ");
    }

    public static function find_morethan_8_brands($docid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE docid={$docid} AND month(month) ={$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016' ");
        if (count($result_array) > 8) {
            return array_slice($result_array, 8);
        } else {
            return FALSE;
        }
    }

    public static function entryExist($id) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE id = {$id}  ");
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

    public static function find_by_month($docid, $month) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE docid={$docid} AND month(month) = '$month' and YEAR(month)= '2016' ");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function count_all($empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE empid='$empid'";
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

    public function update() {
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id = {$this->id}";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function updateTotal() {
        global $database;
        $sql = "UPDATE " . self::$table_name . " SET total = '$this->total' WHERE docid = '$this->docid' ";
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
        $sql = "SELECT COUNT(*)  FROM (SELECT SUM(total)  FROM " . self::$table_name . "  WHERE empid='$empid' AND month(month)= {$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016' GROUP BY `docid` HAVING $total ) AS t ";
        return self::returnCount($sql);
    }

    public static function adminBarGraph($total) {
        $sql = "SELECT COUNT(*) FROM (SELECT SUM(total) FROM doc_buisness_profile WHERE MONTH(MONTH)= {$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016' GROUP BY `docid` HAVING $total ) AS t ";
        return self::returnCount($sql);
    }

    public static function SMdrawBarGraph($sm_empid, $total) {
        global $database;
        $sql = " SELECT COUNT(*) FROM (
                    SELECT SUM(total) FROM doc_buisness_profile WHERE empid IN(  
                        SELECT empid FROM employees WHERE bm_empid IN (
                                SELECT bm_empid FROM bm WHERE sm_empid ='{$sm_empid}'		
                            ) 
                    ) AND MONTH(MONTH)= {$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016' GROUP BY `docid` HAVING $total ) AS t";
        return self::returnCount($sql);
    }

    public static function BMdrawBarGraph($bm_empid, $total) {
        global $database;
        $sql = " SELECT COUNT(*) FROM (
                    SELECT SUM(total) FROM doc_buisness_profile WHERE empid IN(  
			SELECT empid FROM employees WHERE bm_empid = '{$bm_empid}'
                    ) AND MONTH(MONTH) = {$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016' GROUP BY `docid` HAVING $total 
                ) AS t";
        return self::returnCount($sql);
    }

    public static function drawBrandGraph($brandName, $month, $empid) {
        global $database;
        $sql = "SELECT SUM(" . $brandName . ") AS SUM FROM " . self::$table_name . " WHERE month(month) = {$month} AND empid = '{$empid}' and YEAR(month)= '2016' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row['SUM'])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function drawBrandGraph2($docid, $brandName) {
        global $database;
        $sql = "SELECT  FROM " . self::$table_name . " WHERE month(month) = {$_SESSION['_CURRENT_MONTH']} AND docid = '$docid' and YEAR(month)= '2016' ";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        if (is_null($row[0])) {
            return 0;
        } else {
            return array_shift($row);
        }
    }

    public static function drawBrandGraph3($doctorName, $brandName, $month) {

        $sql = "SELECT SUM(" . $brandName . ") AS SUM FROM " . self::$table_name . " WHERE month(month) = '{$month}' AND docid = '{$doctorName}' and YEAR(month)= '2016' ";
        return self::returnCount($sql);
    }

    public static function BMdrawBrandGraph($brandName, $month, $bm_empid) {

        $sql = "SELECT SUM(" . $brandName . ") AS SUM FROM " . self::$table_name . " WHERE month(month) = '{$month}' and YEAR(month)= '2016' AND empid IN (
            SELECT empid FROM employees WHERE bm_empid = '{$bm_empid}'
    )";
        return self::returnCount($sql);
    }

    public static function BMdrawBrandGraph3($doctorName, $brandName, $month) {

        $sql = "SELECT  SUM(" . $brandName . ")FROM " . self::$table_name . " WHERE month(month) = '{$month}' AND docid = '{$doctorName}' and YEAR(month)= '2016' ";
        return self::returnCount($sql);
    }

    public static function SMdrawBrandGraph($brandName, $month, $sm_empid) {
        global $database;
        $sql = "SELECT SUM(" . $brandName . ") FROM " . self::$table_name . " WHERE month(month) = '{$month}'  and YEAR(month)= '2016' AND empid IN (
                SELECT empid FROM employees WHERE bm_empid IN (SELECT bm_empid FROM bm WHERE sm_empid = '{$sm_empid}')
                )";

        return self::returnCount($sql);
    }

    public static function lastMonthBuisness($empid) {
        $sql = " SELECT SUM(total) AS SUM1  FROM " . self::$table_name . " WHERE month(month) ={$_SESSION['_CURRENT_MONTH']} and YEAR(month)= '2016' AND empid = '$empid' ";
        $result_array = QueryWrapper::executeQuery($sql);
        return array_shift($result_array);
    }

    public static function overall_lastmonth_business($month = '') {
        if ($month === '') {
            $month = $_SESSION['_CURRENT_MONTH'];
        }
        $result_array = QueryWrapper::executeQuery("SELECT SUM(total) AS SUM1 FROM " . self::$table_name . " WHERE month(month)= {$month} and YEAR(month)= '2016'");
        return array_shift($result_array);
    }

    public static function Month_wise_Buisness($month, $empid) {
        $sql = " SELECT SUM(total) AS SUM1  FROM " . self::$table_name . " WHERE month(month) ={$month} AND empid = '$empid' and YEAR(month)= '2016' ";
        return self::returnCount($sql);
    }

    public static function brandwise_business($brand_id, $month, $empid) {
        $sql = " SELECT SUM(brand1) AS SUM1  FROM " . self::$table_name . " WHERE month(month) ={$month} AND empid = '$empid' and YEAR(month)= '2016' AND brand_id = '$brand_id' ";
        return self::returnCount($sql);
    }

    public static function lastMonthBusiness_docwise($docid) {
        global $database;
        $sql = " SELECT SUM(total) AS SUM1 FROM " . self::$table_name . " WHERE month(month) ={$_SESSION['_CURRENT_MONTH']}  and YEAR(month)= '2016' AND docid = '$docid' ";
        $result_array = QueryWrapper::executeQuery($sql);
        return array_shift($result_array);
    }

    public static function docwise_business($docid, $month, $type = '') {
        if ($type === '') {
            $sql = " SELECT SUM(total) AS SUM1  FROM " . self::$table_name . " WHERE month(month) ={$month} AND docid = '$docid' and YEAR(month)= '2016' ";
            return self::returnCount($sql);
        } elseif ($type === 'all') {
            $sql = " SELECT *  FROM " . self::$table_name . " WHERE month(month) ={$month} AND docid = '$docid' and YEAR(month)= '2016' ";
            return self::find_by_sql($sql);
        }
    }

    public static function doc_brand_wise_business($brand_id, $docid, $month) {
        $sql = " SELECT SUM(brand1) AS SUM1  FROM " . self::$table_name . " WHERE month(month) ={$month} AND docid = '$docid' and YEAR(month)= '2016' AND brand_id = '$brand_id' ";
        return self::returnCount($sql);
    }

    public static function returnCount($sql) {
        global $database;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function viewProfile($docid) {
        $output = '';
        $output .= '<table cellspacing="0" class="table table-bordered">';

        for ($i = 2; $i <= date("m", time()) + 1; $i++) {
            $BusiProfile = BusiProfile::docwise_business($docid, $i - 1, 'all');
            if (!empty($BusiProfile)) {
                $output .=' <tr><td></td>';

                foreach ($BusiProfile as $item) {
                    if ($item->brand_id != 0) {
                        $output .=' <td style="font-size:11px" >' . ProductList1($item->brand_id) . '</td>';
                    }
                }
                $output .= '<td><strong>Total Business</strong></td>
                                </tr>
                                    <tr>
                                        <td><strong>' . date("M ", mktime(0, 0, 0, $i - 1, 0, date("Y", time()))) . '</strong></td>';

                $total = 0;
                foreach ($BusiProfile as $item) {
                    if ($item->brand_id != 0) {
                        $output .= '<td>' . $item->brand1 . '</td>';
                        $total += $item->total;
                    }
                }
                $output .= '<td> ' . $total . ' </td></tr>';
                unset($BusiProfile);
            }
        }
        $output .= ' </table>';
        return $output;
    }

}
?>