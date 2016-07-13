<?php

session_start();
require_once("../includes/initialize.php");

if (isset($_POST['search_term1'])) {
    $empname = $_POST['search_term1'];
    global $database;
    $sql = "SELECT * FROM doctors WHERE docid IN (
				SELECT docid FROM doc_basic_profile WHERE empid IN (SELECT empid FROM employees WHERE name='{$empname}')
				AND month(DOB) >= month(CURDATE()) AND day(DOB) >= day(CURDATE())
			)";
    $result = $database->query($sql);

    echo "<table class ='table table-bordered table-hover ' id='searchtable'>
		<thead><tr>
		<th>Name</th>
		<th>Mobile</th>
		<th>Area</th>
		<th>Date Of Birth</th>
		</tr></thead><tbody>";

    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo'<td data-title="Name">' . $row['name'] . '&nbsp</td>';
        echo '<td data-title="Mobile">' . $row['mobile'] . '&nbsp</td>';
        echo '<td data-title="Area">' . $row['area'] . '&nbsp</td>';
        $date = BasicProfile::findBirthDate($row['docid']);
        echo '<td data-title="Date Of Birth">' . date('d-m-Y', strtotime($date->DOB)) . '&nbsp</td>';
        echo "</tr>";
    }
    echo "</tbody></table>";
}

$empid = '';
if (isset($_POST['search_term']) && ($_POST['search_term'] == 'Week')) {
    $result = Doctor::NextWeekBirthdays($empid);
    if (!empty($result)) {
        echo "<table class ='table table-bordered table-hover ' id='searchtable' >
	<thead><tr>
	<th>Name</th>
	<th>Mobile</th>
	<th>Area</th>
	<th>Date Of Birth</th>
	</tr></thead><tbody>";

        foreach ($result as $doctor) {
            echo "<tr>";
            echo'<td data-title="Name">' . $doctor->name . '&nbsp</td>';
            echo '<td data-title="Mobile">' . $doctor->mobile . '&nbsp</td>';
            echo '<td data-title="Area">' . $doctor->area . '&nbsp</td>';
            $date = BasicProfile::findBirthDate($doctor->docid);
            echo '<td data-title="Date Of Birth">' . date('d-m-Y', strtotime($date->DOB)) . '&nbsp</td>';
            echo "</tr>";
        }
        echo '</tbody></table>';
    } else {
        echo "Dont have Birthday In next week";
    }
}

if (isset($_POST['search_term']) && ($_POST['search_term'] == 'Month')) {
    $result = Doctor::NextMonthBirthdays($empid);
    if (!empty($result)) {
        echo "<table class ='table table-bordered table-hover ' id='searchtable'>
	<thead><tr>
	<th>Name</th>
	<th>Mobile</th>
	<th>Area</th>
	<th>Date Of Birth</th>
	</tr></thead><tbody>";

        foreach ($result as $doctor) {
            echo "<tr>";
            echo'<td data-title="Name">' . $doctor->name . '&nbsp</td>';
            echo '<td data-title="Mobile">' . $doctor->mobile . '&nbsp</td>';
            echo '<td data-title="Area">' . $doctor->area . '&nbsp</td>';
            $date = BasicProfile::findBirthDate($doctor->docid);
            echo '<td data-title="Date Of Birth">' . date('d-m-Y', strtotime($date->DOB)) . '&nbsp</td>';
            echo "</tr>";
        }
        echo '</tbody></table>';
    } else {
        echo "Dont have Birthday In next month";
    }
}

if (isset($_POST['search_term']) && ($_POST['search_term'] == '3 Months')) {
    $result = Doctor::Next3MonthBirthdays($empid);
    if (!empty($result)) {
        echo "<thead><table class ='table table-bordered table-hover ' id='searchtable'>
	<tr>
	<th>Name</th>	
	<th>Mobile</th>
	<th>Area</th>
	<th>Date Of Birth</th>
	</tr></thead><tbody>";

        foreach ($result as $doctor) {
            echo "<tr>";
            echo'<td data-title="Name">' . $doctor->name . '&nbsp</td>';
            echo '<td data-title="Mobile">' . $doctor->mobile . '&nbsp</td>';
            echo '<td data-title="Area">' . $doctor->area . '&nbsp</td>';
            $date = BasicProfile::findBirthDate($doctor->docid);
            echo '<td data-title="Date Of Birth">' . date('d-m-Y', strtotime($date->DOB)) . '&nbsp</td>';
            echo "</tr>";
        }
        echo '</tbody></table>';
    } else {
        echo "Dont have Birthday In next 3 months";
    }
}
?>