<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$pageTitle = "List All Doctors";
$empName = Employee::find_by_empid($_SESSION['employee']);

$empid = $_SESSION['employee'];
$doctors = Doctor::find_all($empid);
require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">List Of Doctors</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?> 
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="no-more-tables">
        <table class="table table-bordered table-hover " id="searchtable">
            <thead class="cf" >
                <tr>
                    <th>Name</th>
                    <th>MSL Code</th>
                    <th>Class</th>
                    <th>Speciality</th>
                    <th>Email-Id</th>
                    <th class="numeric">Mobile No</th>
                    <th>Area</th>
                    <th>Profile Completed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $page_count = 1;
                foreach ($doctors as $doctor) {
                    $BasicProfile = BasicProfile::find_by_docid($doctor->docid);
                    $total = Doctor:: profile_complete_percentage($doctor->docid);
                    ?>
                    <tr <?php
                    if (isset($_GET['complete'])) {
                        if ($total != 100) {
                            echo 'style ="display : none"';
                        }
                    }
                    ?>>
                        <td data-title="Name"><a href="viewAllProfiles.php?docid=<?php echo $doctor->docid ?>&page=<?php echo $page_count; ?>"><?php echo $doctor->name; ?></a></td>
                        <td data-title="MSL Code"><?php echo isset($BasicProfile->msl_code) ? $BasicProfile->msl_code : "-" ?></td>
                        <td data-title="Class"><?php echo isset($BasicProfile->class) ? $BasicProfile->class : "-" ?></td>
                        <td data-title="Speciality"><?php echo $doctor->speciality; ?></td>
                        <td data-title="Email-Id"><?php echo $doctor->emailid != '' ? $doctor->emailid : "&nbsp"; ?></td>
                        <td data-title="Mobile No"><?php echo $doctor->mobile != '' ? $doctor->mobile : "&nbsp"; ?></td>
                        <td data-title="Area"><?php echo $doctor->area; ?></td>
                        <td data-title="Profile Completed"><?php
                            echo ($total) . " %";
                            ?>
                        </td>
                        <td data-title="Action"><a href="delete_territory.php?delete_doctor=<?php echo $doctor->docid; ?>" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a></td>
                    </tr>
                    <?php
                    $page_count++;
                }
                //}
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once("layouts/TMfooter.php"); ?>
