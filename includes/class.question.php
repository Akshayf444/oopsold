<?php

require_once('database.php');
require_once('ImageManipulator.php');

class Question {

    protected static $table_name = "questions";
    protected static $db_fields = array('id', 'question', 'type', 'options', 'filename', 'created', 'empid', 'is_delete', 'emp_type');
    public $id;
    public $question;
    public $type;
    public $options;
    public $filename;
    public $created;
    public $empid;
    public $is_delete;
    public $emp_type;

    public static function find_all($limit = 0) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE is_delete = 0 ORDER BY id DESC LIMIT $limit , 25 ");
    }

    public static function find_by_id($id = 0) {
        $result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE id={$id} LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_empid($empid) {
        return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE empid ='$empid' ORDER BY id DESC ");
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

    public static function Timeline($Questions) {
        $root = $GLOBALS['site_root'];
        $output = '';

        if (!empty($Questions)) {
            foreach ($Questions as $Question) {
                $Answers = Answer::find_by_qtn_id($Question->id);
                if ($Question->emp_type == 'tm') {
                    $Employee = Employee::find_by_empid($Question->empid);
                    $HQ = $Employee->HQ;
                    $State = $Employee->state;
                } elseif ($Question->emp_type == 'bm') {
                    $Employee = BM::find_by_bmid($Question->empid);
                    $empName = Employee::find_by_bmid($Employee->bm_empid);
                    $HQ = array_shift($empName)->HQ;
                    $State = array_shift($empName)->state;
                } elseif ($Question->emp_type == 'sm') {
                    $Employee = SM::find_by_smid($Question->empid);
                    $empName = Employee::find_by_smid($Employee->sm_empid);
                    $HQ = array_shift($empName)->HQ;
                    $State = array_shift($empName)->state;
                }

                $deletePost = '<div class="pull-right">
                            <div class="dropdown">
                                <button style="background: #fff;border: 0px" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul style="min-width: 82px; padding: 0;left: -59px" class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                    <li role="presentation" ><a role="menuitem" tabindex="-1" href="#" onclick="deleteAnswer(this.id)" id="' . $Question->id . "qtn" . '">Delete</a></li>
                                </ul>
                            </div>
                        </div>';

                $profilePhoto = isset($Employee->profile_photo) && $Employee->profile_photo != '' ? $Employee->profile_photo : "user.png";
                $output .='<div class="timeline-item timeline-item-right">
                                        <div class="timeline-item-content">
                                            <div class="timeline-heading">
                                                <img src="' . $root . '/files/' . $profilePhoto . '"/> 
                                                <b style ="font-size:16px">' . $Employee->name . '</b> asked a question <small style="padding-right: 10px;font-style: italic ; color: #003399"><b>' . time_passed(strtotime($Question->created)) . '</b></small>'
                        . '                                   ';

                if (isset($_SESSION['employee']) && $Question->empid == $_SESSION['employee']) {
                    $output .= $deletePost;
                } elseif (isset($_SESSION['BM']) && $Question->empid == $_SESSION['BM']) {
                    $output .= $deletePost;
                } elseif (isset($_SESSION['SM']) && $Question->empid == $_SESSION['SM']) {
                    $output .= $deletePost;
                }

                $output .= '<div style="margin-left:50px"><b style="color:gray"><small>' . strtoupper($Question->emp_type) . ' ,' . $HQ . ', ' . $State . '</small></b><br/><br/><p style="color: #080">' . $Question->question . '</p></div></div> ';

                if ($Question->type == 'image') {
                    $output .= ' <div class="timeline-body" id="links">                                            
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="#"  data-gallery>
                                            <img src="' . $root . '/posts/' . $Question->filename . '" class="img-responsive img-text"/>
                                        </a>
                                    </div>
                                </div>
                                </div> ';
                }

                $output .= '<div class="timeline-body comments ' . $Question->id . '" >';

                if (!empty($Answers)) {
                    foreach ($Answers as $Answer) {
                        if ($Answer->emp_type == 'tm') {
                            $Employee = Employee::find_by_empid($Answer->empid);
                        } elseif ($Answer->emp_type == 'bm') {
                            $Employee = BM::find_by_bmid($Answer->empid);
                        } elseif ($Answer->emp_type == 'sm') {
                            $Employee = SM::find_by_smid($Answer->empid);
                        }

                        $photo = isset($Employee->profile_photo) && $Employee->profile_photo != '' ? $Employee->profile_photo : "user.png";
                        $output .= '<div class="comment-item">
                                        <img src=" ' . $root . '/files/' . $photo . '"/> 
                                        <p class="comment-head">
                                            <a href="#">' . $Employee->name . '</a> <span class="text-muted">' . time_passed(strtotime($Answer->created)) . '</span>
                                        </p>
                                        <p>' . $Answer->answer . '</p>
                                    </div> ';
                    }
                }


                $output .= '<div class = "comment-write">
                                <textarea class = "form-control question" placeholder = "Write a comment" rows = "1"></textarea>
                                <div class = "pull-right " style = "margin-top: 2px"><input type = "button" class = "btn btn-success btn-xs addAnswer" id = "' . $Question->id . '" value = "post" onclick = "addComment(this.id)"></div>
                            </div>
                            </div>
                        </div>
                    </div> ';
            }
        }

        return $output;
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
            $this->id = $database->insert_id();
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
        $sql .= " WHERE id=" . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    public function delete() {
        global $database;
        $sql = "UPDATE " . self::$table_name . " set is_delete = 1 WHERE id= {$this->id} ";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

}

?>