<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:login.php");
}
require_once("../includes/initialize.php");

if (isset($_POST['search_term'])) {
    $empid = $_POST['search_term'];
    $Doctors = Doctor::find_all($empid);
}
?>
<table class="table table-bordered table-hover ">
    <tr>
        <th style="width:25%">Name</th>
        <th>Speciality</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Area</th>
        <th style="width:10% ">Profiles Completed</th>
    </tr>
    <?php foreach ($Doctors as $doctor): ?>
        <tr>
            <td><a href="BMviewAllProfiles.php?docid=<?php echo $doctor->docid ?>"><?php echo $doctor->name; ?></a></td>
            <td><?php echo $doctor->speciality; ?></td>
            <td><?php echo $doctor->emailid; ?></td>
            <td><?php echo $doctor->mobile; ?></td>
            <td><?php echo $doctor->area; ?></td>
            <td><?php
                $total = Doctor:: profile_complete_percentage($doctor->docid);
                echo ($total) . " %";
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>