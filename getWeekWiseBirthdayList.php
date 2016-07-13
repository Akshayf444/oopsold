<?php session_start();
require_once(dirname(__FILE__)."/includes/initialize.php");

	if(isset($_POST['search_term1'])){
		$empname=$_POST['search_term1'];
		global $database;
		$sql="SELECT * FROM doctors WHERE docid IN (
				SELECT docid FROM doc_basic_profile WHERE empid IN (SELECT empid FROM employees WHERE name='{$empname}')
				AND month(DOB) >= month(CURDATE()) AND day(DOB) >= day(CURDATE())
			)";
		$result = $database->query($sql);

		echo "<table class ='table table-bordered table-hover '>
		<tr>
		<th>Name</th>
		<th>Mobile</th>
		<th>Area</th>
		<th>Date Of Birth</th>
		</tr>";

		while($row = mysqli_fetch_array($result)) {
		  echo "<tr>";
		  echo'<td>' . $row['name'] . '</td>';
		  echo '<td>' . $row['mobile'] . '</td>';
		  echo '<td>' . $row['area'] . '</td>';
		 $date=BasicProfile::findBirthDate($row['docid']);
		  echo '<td>' .date('d-m-Y', strtotime($date->DOB)). '</td>';
		  echo "</tr>";
		}
		echo "</table>";
		}

	$empid='';
	if(isset($_POST['search_term']) && ($_POST['search_term'] == 'Week')){
	$result=Doctor::NextWeekBirthdays($empid);
	if(!empty($result)){
	echo "<table class ='table table-bordered table-hover '>
	<tr>
	<th>Name</th>
	<th>Mobile</th>
	<th>Area</th>
	<th>Date Of Birth</th>
	</tr>";

		foreach ($result as $doctor) {		
		  echo "<tr>";
		  echo'<td>' . $doctor->name. '</td>';
		  echo '<td>' . $doctor->mobile . '</td>';
		  echo '<td>' . $doctor->area . '</td>';
		  $date=BasicProfile::findBirthDate($doctor->docid);
		  echo '<td>' .date('d-m-Y', strtotime($date->DOB)) . '</td>';
		  echo "</tr>";
		}
		}else{
			echo "Dont have Birthday In next week";
		}

	}

		if(isset($_POST['search_term']) && ($_POST['search_term'] == 'Month')){
	$result=Doctor::NextMonthBirthdays($empid);
	if(!empty($result)){
	echo "<table class ='table table-bordered table-hover '>
	<tr>
	<th>Name</th>
	<th>Mobile</th>
	<th>Area</th>
	<th>Date Of Birth</th>
	</tr>";

		foreach ($result as $doctor) {		
		  echo "<tr>";
		  echo'<td>' . $doctor->name. '</td>';
		  echo '<td>' . $doctor->mobile . '</td>';
		  echo '<td>' . $doctor->area . '</td>';
		  $date=BasicProfile::findBirthDate($doctor->docid);
		  echo '<td>' .date('d-m-Y', strtotime($date->DOB)). '</td>';
		  echo "</tr>";
		}
		}else{
			echo "Dont have Birthday In next month";
		}

	}

		if(isset($_POST['search_term']) && ($_POST['search_term'] == '3 Months')){
	$result=Doctor::Next3MonthBirthdays($empid);
	if(!empty($result)){
	echo "<table class ='table table-bordered table-hover '>
	<tr>
	<th>Name</th>
	
	<th>Mobile</th>
	<th>Area</th>
	<th>Date Of Birth</th>
	</tr>";

		foreach ($result as $doctor) {		
		  echo "<tr>";
		  echo'<td>' . $doctor->name. '</td>';
		  echo '<td>' . $doctor->mobile . '</td>';
		  echo '<td>' . $doctor->area . '</td>';
		  $date=BasicProfile::findBirthDate($doctor->docid);
		  echo '<td>' .date('d-m-Y', strtotime($date->DOB)). '</td>';
		  echo "</tr>";
		}
		}else{
			echo "Dont have Birthday In next 3 months";
		}

	}
?>