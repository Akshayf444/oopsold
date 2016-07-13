<?php

// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('database.php');

class Services {

    protected static $table_name = "services";
    public static $db_fields = array('docid', 'name', 'empid', 'id', 'aushadh', 'other_services', 'AOIC', 'DOC', 'ESCRS', 'WGC', 'WOC', 'Other', 'service', 'factors', 'action_plan', 'special_rate');
    public $docid;
    public $name;
    public $empid;
    public $id;
    public $aushadh;
    public $other_services;
    public $AOIC;
    public $DOC;
    public $ESCRS;
    public $WGC;
    public $WOC;
    public $Other;
    public $service;
    public $factors;
    public $action_plan;
    public $special_rate;

    // Common Database Methods
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_docid($docid = "") {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE docid={$docid} LIMIT 1");
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
        $sql = "SELECT COUNT(*) FROM services s INNER JOIN doctors d ON d.docid = s.docid WHERE d.is_delete = 0 AND s.empid='$empid'";
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

        // - single-quotes around all values
        // - escape all values to prevent SQL injection

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
        $sql .= " WHERE id=" . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function drawPieChart($empid, $service) {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE empid='$empid' AND service='$service'";
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function SMdrawPieChart($sm_empid, $service) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `services` WHERE empid IN (
		SELECT empid FROM employees WHERE bm_empid IN (SELECT bm_empid FROM bm WHERE sm_empid ='{$sm_empid}')
	) AND service ='$service' ";

        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function admindrawPieChart($service) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `services` WHERE  service ='$service' ";

        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function BMdrawPieChart($bm_empid, $service) {
        global $database;
        $sql = "SELECT COUNT(*) FROM `services` WHERE empid IN (
		SELECT empid FROM employees WHERE bm_empid ='{$bm_empid}'
	) AND service ='$service' ";

        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    public static function viewProfile($service) {
        $output = '';
        if (!empty($service)) {
            $output .= '<table cellspacing="0" class="table table-bordered" style="margin-top: 1em">
                        <tr>
                            <td>Services Provided To Doctor:</td>
                            <td>
                                '. $service->aushadh   .'
                            </td>
                        </tr>
                        <tr>
                            <td>Activities With Doctor</td>
                            <td>
                                '. $service->factors   .'
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">Services By Other Competing Companies</th>
                        </tr>	
                        <tr>
                            <td>High Value Gifts  </td>	
                            <td>'. $service->AOIC   .'</td>
                        </tr>
                        <tr>
                            <td>Special Rate  </td>
                            <td>'. $service->DOC   .'</td>
                        </tr>
                        <tr>
                            <td>Bulk Sampling  </td>
                            <td>'. $service->ESCRS   .'</td>
                        </tr>
                        <tr>
                            <td>Post-op pouches / cards </td>
                            <td>'. $service->WGC   .'</td>
                        </tr>
                        <tr>
                            <td>Journals/Books/Online Subscription</td>
                            <td>'. $service->WOC   .'</td>
                        </tr>
                        <tr>
                            <td>Conferences</td>
                            <td>'. $service->Other   .'</td>
                        </tr>
                        </tr>
                    </table>';
        }  else {
            $output .='';
        }
        
        return $output;
    }

}

?>