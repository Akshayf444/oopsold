<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");

if (isset($_POST['search_term'])) {
    $empid = $_POST['search_term'];
    $Doctors = Doctor::find_all($empid);
}
?>
<table class="table table-bordered table-hover " id="searchtable">
    <thead>
        <tr>
            <th style="width:25%">Name</th>
            <th>Speciality</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Area</th>
            <th style="width:10% ">Profiles Completed</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($Doctors as $doctor): ?>
            <tr>
                <td data-title="Name"><a href="SMviewAllProfiles.php?docid=<?php echo $doctor->docid ?>"><?php echo $doctor->name; ?></a></td>
                <td data-title="Speciality"><?php echo $doctor->speciality; ?>&nbsp;</td>
                <td data-title="Email"><?php echo $doctor->emailid; ?>&nbsp;</td>
                <td data-title="Mobile"><?php echo $doctor->mobile; ?>&nbsp;</td>
                <td data-title="Area"><?php echo $doctor->area; ?>&nbsp;</td>
                <td data-title="Profiles Completed"><?php
                    $total = Doctor:: profile_complete_percentage($doctor->docid);
                    echo ($total) . " %";
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>