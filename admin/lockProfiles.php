<?php require_once("../includes/initialize.php");
$field =preg_split("/_/",$_POST['search_term']);
$status=1;
if (isset($_POST['search_term']) && ($field[1] == 'basic')) {
	$employee = new Employee();
	$employee->lockBasic($field['0'],$status);
}
if (isset($_POST['search_term']) && ($field[1] == 'buisness')) {
	$employee = new Employee();
	$employee->lockBuisness($field['0'],$status);
}
if (isset($_POST['search_term']) && ($field[1] == 'service')) {
	$employee = new Employee();
	$employee->lockService($field['0'],$status);
}
if (isset($_POST['search_term']) && ($field[1] == 'academic')) {
	$employee = new Employee();
	$employee->lockAcademic($field['0'],$status);
}
?>