<?php
session_start();

require_once(dirname(__FILE__) . "/includes/initialize.php");
if (isset($_GET['id'])) {
    require_once(dirname(__FILE__) . "/includes/class.territory.php");
    require_once(dirname(__FILE__) . "/includes/functions.php");
    $terr_id = $_GET['id'];
    $delete_terr = new Territory();
    $delete_terr->id = $terr_id;
    if ($delete_terr->delete()) {
        flashMessage("Territory Deleted Successfully.", 'success');
        redirect_to('Profile.php');
    }
} elseif (isset($_GET['delete_doctor'])) {
    $docid = $_GET['delete_doctor'];
    $deleteDoc = new Doctor();
    $deleteDoc->docid = $docid;
    if ($deleteDoc->delete()) {
        flashMessage('Doctor Deleted Successfully.', 'success');
        redirect_to('listAllDoctors.php');
    }
} elseif (isset($_GET['update_doctor_mobile'])) {
    $docid = $_GET['update_doctor_mobile'];
    $mobile = $_GET['mobile'];
    $doctor = Doctor::updateMobile($docid, $mobile);
    $doctor = Doctor::find_by_docid($docid);
    ?>
    <i class="fa fa-mobile"> </i><span ><?php echo " " . $doctor->mobile; ?> <button  class="btn btn-xs btn-info" id="<?php echo $docid; ?>" onclick="editMobile(this.id)"><i class="fa fa-pencil"></i></button> </span>
    <input type="text" class="form-control" id="mobileArea1" value="<?php echo $doctor->mobile; ?>" style="display:none;width: 80%">
    <input type="button" class="btn btn-info btn-xs" id="mobileArea2" value="Edit" style="display: none" ><br>

    <?php
} elseif (isset($_GET['update_doctor_email'])) {
    $docid = $_GET['update_doctor_email'];
    $email = $_GET['email'];
    $doctor = Doctor::updateEmail($docid, $email);
    $doctor = Doctor::find_by_docid($docid);
    ?>
    <i class="fa fa-envelope"> </i><span ><?php echo " " . $doctor->emailid; ?> <button class="btn btn-xs btn-info" id="<?php echo 'email' . $docid; ?>" onclick="editEmail(this.id)"><i class="fa fa-pencil"></i></button> </span>
    <input type="text" class="form-control" id="emailArea1" value="<?php echo $doctor->emailid; ?>" style="display:none;width: 80%">
    <input type="button" class="btn btn-info btn-xs" id="emailArea2" value="Edit" style="display: none" ><br>
    <?php
} elseif (isset($_GET['update_doctor_area'])) {
    $docid = $_GET['update_doctor_area'];
    $area = $_GET['area'];
    $doctor = Doctor::updateArea($docid, $area);
    $doctor = Doctor::find_by_docid($docid);
    ?>
    <i class="fa fa-map-marker"> </i><span ><?php echo " " . $doctor->area; ?> <button class="btn btn-xs btn-info" id="<?php echo 'area' . $docid; ?>" onclick="editarea(this.id)"><i class="fa fa-pencil"></i></button> </span>
    <select class="form-control" id="areaArea1" style="display: none" >
        <?php
        $AreaList = areaList();
        echo $AreaList;
        ?>
    </select>
    <input type="button" class="btn btn-info btn-xs" id="areaArea2" value="Edit" style="display: none" ><br>

    <?php
}