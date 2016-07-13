<?php
require_once("../includes/initialize.php");
$brandName = $_POST['search_term2'];
$employees = Employee::find_by_bmid($brandName);
?>
<select  onchange="Search2()" class="employee1 form-control">
    <option value="select">Select Employee</option>
    <?php foreach ($employees as $employee): ?>
        <option value="<?php echo $employee->empid; ?>"><?php echo $employee->name; ?></option>
    <?php endforeach; ?>
</select>
