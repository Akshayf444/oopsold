<?php require_once('database.php'); ?>
<?php

class AcaProfile {

    protected static $table_name = "doc_academic_profile";
    protected static $db_fields = array('docid', 'name', 'empid', 'id', 'media', 'journal', 'subscription', 'materials', 'activities', 'local', 'intern');
    public $docid;
    public $name;
    public $empid;
    public $id;
    public $media;
    public $journal;
    public $subscription;
    public $materials;
    public $activities;
    public $local;
    public $intern;

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
        //echo $sql;
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    public static function count_all($empid) {
        global $database;
        $sql = "SELECT COUNT(*) FROM doc_academic_profile da INNER JOIN doctors d ON d.docid = da.docid  WHERE d.is_delete = 0 AND da.empid='$empid'";
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
        $sql .= " WHERE docid=" . $database->escape_value($this->docid);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public static function viewProfile($academicProfile) {
        $output = '';
        if (!empty($academicProfile)) {
            $output .='<table cellspacing="0" class="table table-bordered" style="margin-top: 1em">
                        <tr> <td>Preferred Academic Media:</td>
                            <td> ' . $academicProfile->media . '  </td>
                        </tr>
                        <tr>
                            <td>Scientific Journal:</td>
                            <td>     ' . $academicProfile->journal . '  </td>
                        </tr>
                        <tr>
                            <td>Online Subscriptions:</td>
                            <td> ' . $academicProfile->subscription . ' </td>
                        </tr>
                        <tr>
                            <td>Interest in Patient Education Materials</td>	
                            <td> ' . $academicProfile->materials . '</td>
                        </tr>
                        <tr>
                            <td>Activities</td>	
                            <td>' . $academicProfile->activities . '</td>
                        </tr>
                        </td>
                        </tr>
                        <tr>
                            <th>Professional Association</th>
                            <td>
                        <tr>
                            <td>Local:</td>
                            <td> ' . $academicProfile->local . '</td>
                        </tr>
                        <tr>
                            <td>International :</td>
                            <td>' . $academicProfile->intern . '</td>

                        </tr>

                        </tr>
                    </table>';
        }
        return $output;
    }

}

?>