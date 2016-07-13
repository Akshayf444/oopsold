<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$bm_empid = $_SESSION['BM'];
$allDoctors = Doctor::find_all_doctors($bm_empid);
$employees = Employee::find_by_bmid($bm_empid);

$pageTitle = "View All Doctors";

require_once(dirname(__FILE__) . "/layouts/BMheader.php");
?>

<script>
    function Search() {
        $('#animation').show();
        var search_term = $(".employee").val();
        $.post('BMgetAllDoctorsProfile.php', {search_term: search_term}, function (data) {
            $('#animation').hide();
            $('.table-responsive').html(data);
            $('.result .employee').remove();
        });
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">List Of Doctors</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">
        <select  onchange="Search()" class="employee form-control">
            <option value="select">Select Employee</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?php echo $employee->name; ?>"><?php echo $employee->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="row" style="margin-top:1em;">
    <div class="result col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover ">
                <tr>
                    <th style="width:25% " >Name</th>
                    <th>Speciality</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Area</th>
                    <th style="width:10% " >Profiles Completed</th>
                </tr>
                <?php foreach ($allDoctors as $doctor): ?>
                    <tr>
                        <td><a href="BMviewAllProfiles.php?docid=<?php echo $doctor->docid ?>"><?php echo $doctor->name; ?></a></td>
                        <td><?php echo $doctor->speciality; ?></td>
                        <td><?php echo $doctor->emailid; ?></td>
                        <td><?php echo $doctor->mobile; ?></td>
                        <td><?php echo $doctor->area; ?></td>
                        <td><?php
                            $basicCount1 = Doctor:: count_basic_profile($doctor->docid);
                            $buisnessCount1 = Doctor:: count_buisness_profile($doctor->docid);
                            $academicCount1 = Doctor:: count_academic_profile($doctor->docid);
                            $serviceCount1 = Doctor:: count_service($doctor->docid);

                            $total = $basicCount1 + $buisnessCount1 + $academicCount1 + $serviceCount1;
                            echo ($total / 4 * 100) . " %";
                            ?> </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php require_once(dirname(__FILE__) . "/layouts/BMfooter.php"); ?>