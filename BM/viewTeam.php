<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$final = 0;
$bm_empid = $_SESSION['BM'];
$bmName = BM::find_by_bmid($bm_empid);


$pageTitle = "View Team";
$empcount = Employee::count_all($bm_empid);
$doctorCount = BM::count_all_doctors($bm_empid);
//Total profiles for one BM
$employees = Employee::find_by_bmid($bm_empid);
$totalCount = BM::totalProfileCount($bm_empid);
require_once("BMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Your Team</h1>
    </div>                <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover ">
                <tr>
                    <th>Emp-Id</th>
                    <th>Emp Name</th>
                    <th>No of Doctors</th>
                    <th>No of Profiles</th>
                </tr>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?php echo $employee->cipla_empid; ?></td>
                        <td><?php echo $employee->name; ?></td>
                        <td><?php echo $doctorCount = Doctor::count_all($employee->empid); ?></td>
                        <td><?php echo $doctors= Doctor::completed($employee->empid);?></td>

                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php require_once("BMfooter.php"); ?>