<?php
require_once(dirname(__FILE__) . "/includes/initialize.php");
require_once(dirname(__FILE__) . "/excel/reader.php");

if (isset($_POST['submit']) && $_POST['submit'] != "") {

    $path = "files/";
    $name = $_FILES['file']['name'];
    $size = $_FILES['file']['size'];

    list($txt, $ext) = explode(".", $name);

    //$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
    $actual_image_name = $name;
    $src = "files/" . $actual_image_name;
    $tmp = $_FILES['file']['tmp_name'];

    error_reporting(E_ALL ^ E_NOTICE);

    if (move_uploaded_file($tmp, $path . $actual_image_name)) {
        //echo 'hi';
        error_reporting(E_ALL ^ E_NOTICE);
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');
        //$data->read('Senator.xls');
        $data->read($src);

        $data->sheets[0]['numRows'];
        $empid = '';
        $dob = '';


        //die;
        //for ($a = 0; $a < count($data->sheets); $a++) 
        for ($i = 1; $i < $data->sheets[0]['numRows']; $i++) {
            $empid = $data->sheets[0]['cells'][$i + 1][1];
            $dob = $data->sheets[0]['cells'][$i + 1][2];

            $dSql = "update employees set DOB = '$dob' where empid = '$empid'";
            QueryWrapper::executeQuery2($dSql);
            
        }
    } else {
        echo "failed";
    }
} //if isset() end here 
?>
<form method="post" enctype="multipart/form-data" action="#" id="importUser">
    <input type="file" name="file" required="required"/>
    <input type="submit" class="button" name="submit" value="Import Data" />  
</form>