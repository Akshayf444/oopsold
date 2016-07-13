<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
if (isset($_POST['search_term']) && !empty($_POST['search_term'])) {
    $doctorNames = Doctor::SearchName($_POST['search_term'], $_SESSION['employee']);
}
?>