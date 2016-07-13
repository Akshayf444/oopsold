<?php

require_once("../includes/initialize.php");
$field = preg_split("/\s/", $_POST['search_term']);

if (isset($_POST['search_term']) && $field[0] == 'TM') {
    $empid = $field[1];
    $employee = Employee::cipla_empid($empid);
    if (!empty($employee)) {
        echo "<table class='table table-responsive'><tr>";
        echo '</tr><tr><th>Name</th><td>' . $employee->name . '</td>';
        echo '</tr><tr><th>Emaild</th><td>' . $employee->emailid . '</td>';
        echo '</tr><tr><th>Mobile</th><td>' . $employee->mobile . '</td>';
        echo '</tr><tr><th>City</th><td>' . $employee->city . '</td>';
        echo '</tr><tr><th>State</th><td>' . $employee->state . '</td>';
        echo '</tr><tr><th>Zone </th><td>' . $employee->zone . '</td>';
        echo '</tr><tr><th>Region </th><td>' . $employee->region . '</td>';
        echo '</tr><tr><th>HQ </th><td>' . $employee->HQ . '</td>';
        echo "</tr></table>";
    } else {
        echo "Employee Details Not Found.";
    }
} else {
    if (isset($_POST['search_term']) && $field[0] == 'BM') {
        $empid = $field[1];
        $employee = BM::find_by_bmid($empid);
        if (!empty($employee)) {
            echo "<table class='table table-responsive'><tr>";
            echo '</tr><tr><th>Name</th><td>' . $employee->name . '</td>';
            echo '</tr><tr><th>Emaild</th><td>' . $employee->emailid . '</td>';
            echo '</tr><tr><th>Mobile</th><td>' . $employee->mobile . '</td>';
            echo "</tr></table>";
        } else {
            echo "BM Details Not Found.";
        }
    } else {
        if (isset($_POST['search_term']) && $field[0] == 'SM') {
            $empid = $field[1];
            $employee = SM::find_by_smid($empid);
            if (!empty($employee)) {
                echo "<table class='table table-responsive'><tr>";
                echo '</tr><tr><th>Name</th><td>' . $employee->name . '</td>';
                echo '</tr><tr><th>Emaild</th><td>' . $employee->emailid . '</td>';
                echo '</tr><tr><th>Mobile</th><td>' . $employee->mobile . '</td>';
                echo "</tr></table>";
            } else {
                echo "SM Details Not Found.";
            }
        }
    }
}