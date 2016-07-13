<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");

$bm_empid = $_SESSION['BM'];
$doctors = Doctor::find_all_doctors($bm_empid);
?>
<table>
    <tr>
        <th>Name</th>
        <th>MSL Code</th>
        <th>Class</th>
        <th>Speciality</th>
        <th>Email-Id</th>
        <th>Mobile No</th>
        <th>Area</th>
        <th>Profile Completed</th>
    </tr>
    <?php
    foreach ($doctors as $doctor) {
        $BasicProfile = BasicProfile::find_by_docid($doctor->docid);
        $total = Doctor:: profile_complete_percentage($doctor->docid);
        ?>
        <tr >
            <td><?php echo $doctor->name; ?></td>
            <td><?php echo isset($BasicProfile->msl_code) ? $BasicProfile->msl_code : "-" ?></td>
            <td><?php echo isset($BasicProfile->class) ? $BasicProfile->class : "-" ?></td>
            <td><?php echo $doctor->speciality; ?></td>
            <td><?php echo $doctor->emailid; ?></td>
            <td><?php echo $doctor->mobile; ?></td>
            <td><?php echo $doctor->area; ?></td>
            <td><?php echo ($total) . " %"; ?>
            </td>
        </tr>
    <?php } ?>
</table>