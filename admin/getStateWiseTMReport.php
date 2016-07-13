<?php

session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$final = 0;
if (isset($_POST['search_term'])) {
    $state = $_POST['search_term'];
    $employees = Employee::find_all_by_state($state);
    if (!empty($employees)) {
        echo"<table class='table table-bordered'>
	<tr>
    <th>BM-Id</th>
    <th>Emp-Id</th>
    <th>Emp Name</th>
    <th>No of Doctors</th>
    <th>No of Completed Profiles</th>
    <th>Completed Basic Profiles</th>
    <th>Completed Academic Profiles</th>
    <th>Completed Service Profiles</th>
	</tr>";
        foreach ($employees as $employee) {
            echo "<tr>";
            echo '<td>' . $employee->bm_empid . '</td>';
            echo '<td>' . $employee->empid . '</td>';
            echo '<td>' . $employee->name . '</td>';
            $doctorCount = Doctor::count_all($employee->empid);
            echo '<td>' . $doctorCount . '</td>';
            $doctors = Doctor::find_all($employee->empid);
            foreach ($doctors as $doctor) {
                $total = Doctor::totalProfileCount($doctor->docid);
                if (($total % 4) == 0) {
                    $final+=1;
                } else {
                    $final+=0;
                }
            }
            echo '<td>' . $final;
            '</td>';
            $final = 0;
            $basicCount = Employee::count_basic_profile($employee->empid);
            $serviceCount = Employee::count_service($employee->empid);
            $academicCount = Employee::count_academic_profile($employee->empid);
            echo '<td>' . $basicCount . '</td>';
            echo '<td>' . $serviceCount . '</td>';
            echo '<td>' . $academicCount . '</td>';
            echo '</tr>';
        }
        echo "</table>";
    } else {
        echo "Employee details not found.";
    }
}
?>