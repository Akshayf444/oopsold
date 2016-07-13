<?php require_once("../includes/initialize.php");
$empName=$_POST['search_term'];
$doctors=Doctor::find_by_empname($empName);
?>
<form action ='BMbrandWiseTrend.php' method="post">
<select name="doctor_name" onchange="this.form.submit()" class="form-control">
<option value="select">Select Doctor</option>
<?php foreach($doctors as $doctor): ?>
<option value="<?php echo $doctor->name;?>"><?php echo $doctor->name;?></option>
<?php endforeach; ?>
</select>
</form>