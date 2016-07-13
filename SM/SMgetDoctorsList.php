<?php
require_once("../includes/initialize.php");
$empName = $_POST['search_term'];
$doctors = Doctor::find_all($empName);
if (!empty($doctors)) {    ?>
    <option value="0">Select Doctor</option>
    <?php foreach ($doctors as $doctor): ?>
        <option value="<?php echo $doctor->docid; ?>"><?php echo $doctor->name; ?></option>
    <?php endforeach; ?>

    <?php
}else {
    echo "doctor entry dosnt exist for this employee";
}?>