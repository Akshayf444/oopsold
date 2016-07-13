<?php

// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('database.php');

class User {

    protected static $table_name = "users";
    protected static $db_fields = array('name', 'emailid', 'password', 'mobile', 'DOB', 'DOA', 'address', 'city', 'state');
    public $name;
    public $emailid;
    public $password;
    public $mobile;
    public $DOB;
    public $DOA;
    public $address;
    public $city;
    public $state;

    public function full_name() {
        if (isset($this->name)) {
            return $this->name;
        } else {
            return "";
        }
    }

    public static function authenticate($emailid = "", $password = "") {
        global $database;
        $emailid = $database->escape_value($emailid);
        $password = $database->escape_value($password);

        $sql = "SELECT * FROM users ";
        $sql .= "WHERE BINARY emailid = '{$emailid}' ";
        $sql .= "AND BINARY password = '{$password}' ";
        $sql .= "LIMIT 1";
        $result_array = self::find_by_sql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    // Common Database Methods
    public static function find_all() {
        return self::find_by_sql("SELECT * FROM " . self::$table_name);
    }

    public static function find_by_emailid($emailid = 0) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE emailid='$emailid' LIMIT 1");
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

    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name;
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

    public static function zoneWiseReport($zone) {

        $sql = "SELECT COUNT(d.`docid`) AS doctor_count,COUNT(db.docid) AS basic_count,COUNT(ser.docid) AS service_count,COUNT(da.docid) AS academic_count,COUNT(cmpt.docid) AS cmpt_count FROM doctors d "
                . "INNER JOIN employees e ON d.empid = e.`empid` "
                . "LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` "
                . "LEFT JOIN services ser ON d.`docid` = ser.`docid` "
                . "LEFT JOIN `doc_academic_profile` da ON d.`docid` = da.`docid` "
                . "LEFT JOIN competitors cmpt ON d.`docid` = cmpt.`docid`  WHERE e.zone = '$zone' ";

        $result_array = QueryWrapper::executeQuery($sql);
        return array_shift($result_array);
    }

    public static function zonewiseCompletedProfiles($zone) {
        $sql = "SELECT COUNT(d.`docid`) AS doctor_count FROM doctors d "
                . "INNER JOIN employees e ON d.empid = e.`empid` "
                . "INNER JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` "
                . "INNER JOIN services ser ON d.`docid` = ser.`docid` "
                . "INNER JOIN `doc_academic_profile` da ON d.`docid` = da.`docid` "
                . "INNER JOIN competitors cmpt ON d.`docid` = cmpt.`docid`  WHERE e.zone IN ('$zone')";
        $result_array = QueryWrapper::executeQuery($sql);
        return array_shift($result_array);
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
    //	}

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

    public function update() {
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
        $sql .= " WHERE id=" . $database->escape_value($this->id);
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

    public static function sendmail($emailid, $message, $subject) {

        $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

        $mail->IsSMTP(); // telling the class to use SMTP

        try {
            $body = file_get_contents(dirname(__FILE__) . "/happyBirthday.html");
            $mail->SMTPAuth = true;                  // enable SMTP authentication
            $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
            $mail->Host = "smtpout.asia.secureserver.net";      // sets GMAIL as the SMTP server
            $mail->Port = 465;                   // set the SMTP port for the GMAIL server
            $mail->Username = "m@techvertica.in";  // GMAIL username
            $mail->Password = "Priyanka@123";            // GMAIL password

            $mail->FromName = "Foresight Admin";
            $mail->From = "m@techvertica.in";
            $mail->AddAddress($emailid, "Foresight Admin");

            $mail->Subject = $subject;

            if ($subject == "Aniversary Notification") {
                $mail->AddAttachment(dirname(__FILE__) . "/Aniversary.jpg");
            } else {
                $mail->AddAttachment(dirname(__FILE__) . "/download.jpg");
            }
            $mail->MsgHTML($body);

            $mail->Body = <<<EMAILBODY

Hello Dear

	{$message}

EMAILBODY;

            $mail->IsHTML(true);
            $mail->Send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }

    public static function sm_bm_tm_exist($empid) {
        $found_tm = Employee::find_by_empid($empid);
        $found_bm = BM::find_by_bmid($empid);
        $found_sm = SM::find_by_smid($empid);
        if ($found_tm) {
            return TRUE;
        } elseif ($found_bm) {
            return TRUE;
        } elseif ($found_sm) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>