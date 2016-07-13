<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$doctors = Doctor::find_all($empid);

require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Last Month Business Details</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover ">
                <tr>
                    <th>Name</th>
                    <th>MSL Code</th>
                    <th>Class</th>
                    <th>Email-Id</th>
                    <th>Mobile No</th>
                    <th>Area</th>
                    <th>Speciality</th>
                    <th>Total Business</th>
                    <th>Action</th>

                </tr>
                <?php
                foreach ($doctors as $doctor){
                    $BasicProfile = BasicProfile::find_by_docid($doctor->docid);
                    $lastMonthBusiness = BusiProfile::docwise_business($doctor->docid, $_SESSION['_CURRENT_MONTH']);
                    ?>
                    <tr>
                        <td><?php echo $doctor->name; ?></td>
                        <td><?php echo isset($BasicProfile->msl_code) ? $BasicProfile->msl_code : "-" ?></td>
                        <td><?php echo isset($BasicProfile->class) ? $BasicProfile->class : "-" ?></td>

                        <td><?php echo $doctor->emailid; ?></td>
                        <td><?php echo $doctor->mobile; ?></td>
                        <td><?php echo $doctor->area; ?></td>
                        <td><?php echo $doctor->speciality; ?></td>
                        <td><?php echo isset($lastMonthBusiness) ? $lastMonthBusiness : "-"; ?></td>
                        <td>
                            <a href="viewBusinessProfile.php?docid=<?php echo $doctor->docid; ?>" class="btn btn-info btn-xs"> View Business</a><br/>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
<?php require_once("layouts/TMfooter.php"); ?>