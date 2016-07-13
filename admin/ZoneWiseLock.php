<?php session_start();    
if (!isset($_SESSION['admin'])){ header('Location: login.php');} 
require_once("../includes/initialize.php");
$final=0;
	$zone=$_POST['search_term'];
	$employees=Employee::find_all_by_zone($zone);
	if(!empty($employees)){
		echo "<table>
	<tr>
	<th>SM-Id</th>
        <th>SM-Name</th>
        <th>BM-Id</th>
        <th>BM-Name</th>
        <th>Emp-Id</th>
        <th>Emp Name</th>
        <th>No of Doctors</th>
        <th>Lock Basic Profile</th>
        <th>Lock Service Profile</th>
        <th>Lock Buisness Profile</th>
        <th>Lock academic Profile</th>
	</tr>";
	foreach($employees as $employee){
		echo "<tr>";
		echo '<td>'. $employee->bm_empid.'</td>';
		echo '<td>'. $employee->empid.'</td>';
		echo '<td>'. $employee->name.'</td>';

		echo '<td><input type="checkbox" name="lockbasic[]"></td>
                        <td><input type="checkbox" name="lockservice[]"></td>
                        <td><input type="checkbox" name="lockbuisness[]"></td>
                        <td><input type="checkbox" name="lockacademic[]"></td>
            </tr>';
		
	}
	echo "</table>";
}
 else{
	echo "Employee details not found.";
}
?>