<?php

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
    require_once(dirname(__FILE__) . "/includes/initialize.php");
    $docid = $_GET['delete_doctor'];
    $deleteDoc = new Doctor();
    $deleteDoc->docid = $docid;
    if ($deleteDoc->delete()) {
        flashMessage('Doctor Deleted Successfully.', 'success');
        redirect_to('listAllDoctors.php');
    }
}