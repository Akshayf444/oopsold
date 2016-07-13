<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$pageTitle = "Manage Academic Profile";
$empName = Employee::find_by_empid($_SESSION['employee']);

$empid = $_SESSION['employee'];
$doctors = Doctor::find_all($empid);
?>
<?php require_once("layouts/TMheader.php"); ?>
<style>
    @media only screen and (max-width: 800px) {
        #no-more-tables td{
            padding-left: 35%;        
        }
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Manage Academic Profile</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="no-more-tables">
        <div class="">
            <table class="table table-bordered table-hover " id="searchtable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Speciality</th>
                        <th>Email-Id</th>
                        <th>Mobile No</th>
                        <th>Area</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $page = 1;
                foreach ($doctors as $doctor):
                    ?>
                    <tr>
                        <td data-title="Name"><?php echo $doctor->name; ?></td>
                        <td data-title="Speciality"><?php echo $doctor->speciality; ?></td>
                        <td data-title="Email-Id"><?php echo $doctor->emailid != '' ? $doctor->emailid : "&nbsp"; ?></td>
                        <td data-title="Mobile No"><?php echo $doctor->mobile != '' ? $doctor->mobile : "&nbsp"; ?></td>
                        <td data-title="Area"><?php echo $doctor->area; ?></td>

                        <td data-title="Action"><?php
                            $academicProfile = AcaProfile::find_by_docid($doctor->docid);
                            if (!empty($academicProfile)) {
                                ?>
                                <a href="viewAcademicProfile.php?docid=<?php echo $doctor->docid; ?>&page=<?php echo $page; ?>" class="btn btn-primary btn-xs"> View Profile</a><br/>
                                <?php
                                $page++;
                            } else {
                                ?>

                                <a href="academicProfile.php?docid=<?php echo $doctor->docid; ?>" class="btn btn-danger btn-xs">Add Profile</a><br/>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                    </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once("layouts/TMfooter.php"); ?>