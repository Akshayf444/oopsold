<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$final = 0;
$smName = SM::find_by_smid($_SESSION['SM']);
$sm_empid = $_SESSION['SM'];

$BMs = BM::find_all($sm_empid);
$employees = Employee::find_by_smid($sm_empid);
$pageTitle = "Your Team";
require_once("SMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Your Team</h1>
    </div>                <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="table-responsive" id="no-more-tables">
            <table class="table table-bordered table-hover ">
                <thead>
                    <tr>
                        <th style="width:10%">BM-Id</th>
                        <th style="width:10%">Emp-Id</th>
                        <th>Emp Name</th>
                        <th style="width:10%">No of Doctors</th>
                        <th style="width:10%">No of Profiles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td data-title="BM-Id"><?php echo $employee->bm_empid; ?>&nbsp;</td>
                            <td data-title="Emp-Id"><?php echo $employee->cipla_empid; ?>&nbsp;</td>
                            <td data-title="Emp Name"><?php echo $employee->name; ?>&nbsp;</td>
                            <td data-title="No of Doctors"><?php echo $doctorCount = Doctor::count_all($employee->empid); ?>&nbsp;</td>
                            <td data-title="No of Profiles"><?php echo $doctors = Doctor::completed($employee->empid); ?>&nbsp;</td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
require_once("SMfooter.php");
