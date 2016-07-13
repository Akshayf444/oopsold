<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");

$empid = $_SESSION['employee'];

$pageTitle = "Search Results";
$empName = Employee::find_by_empid($empid);

if (isset($_POST['name'])) {

    $name = trim($_POST['name']);
    $doctors = Doctor::Search($empid, $name);
}
require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Search Results</h1>
    </div>      <!-- /.col-lg-12 -->
</div>

<?php
if (empty($doctors)) {
    echo "Record Dosn't Exist";
} else {
    ?>

    <?php //echo output_message($message);  ?>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 " id="no-more-tables">
            <table  class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email-Id</th>
                        <th>Mobile No</th>
                        <th>Area</th>
                        <th>Speciality</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctors as $doctor): ?>
                        <tr>
                            <td data-title="Name"><a href="viewAllProfiles.php?docid=<?php echo $doctor->docid ?>"><?php echo $doctor->name; ?></a></td>
                            <td data-title="Email-Id"><?php echo $doctor->emailid; ?></td>
                            <td data-title="Mobile No"><?php echo $doctor->mobile; ?></td>
                            <td data-title="Area"><?php echo $doctor->area; ?></td>
                            <td data-title="Speciality"><?php echo $doctor->speciality; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>
<?php require_once("layouts/TMfooter.php"); ?>