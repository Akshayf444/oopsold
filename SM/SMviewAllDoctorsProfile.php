<?php
session_start();

if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$sm_empid = $_SESSION['SM'];
$allDoctors = Doctor::find_all_BMdoctors($sm_empid);
$employees = SM::find_all_employees($sm_empid);
$allBM = BM::find_all($sm_empid);

$pageTitle = "List Of Doctors";
require_once("SMheader.php");
?>

<script>
    function Search2() {
        $(".result").css("background", " url('images/loader.gif') no-repeat scroll center center ");
        var search_term = $(".employee1").val();
        $.post('SMgetAllDoctorsProfile.php', {search_term: search_term}, function (data) {
            $('.result').css("background", "#fff");
            $('.table-responsive').html(data);
            $('.result .employee2').remove();
            $('.result .employee1').remove();
            oTable = $('#searchtable').dataTable({
                "bPaginate": false,
                "bInfo": false
            });
        });
    }
</script>
<script>
    function Search1() {
        $("#employees").css("background", " url('images/loader.gif') no-repeat scroll center center ");
        var search_term2 = $(".employee2").val();
        $.post('getData.php', {search_term2: search_term2}, function (data) {

            $('#employees').css("background", "#fff");
            $('#employees').html(data);
            $('.result .employee1').remove();
            $('.result .employee2').remove();
        });
    }

</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">List Of Doctors</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">

    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8 " <?php
    if (isset($_GET['complete'])) {
        echo 'style = "display:none" ';
    }
    ?>>
        <select  onchange="Search1()" class="employee2 form-control">
            <option value="">Select BM</option>
            <?php foreach ($allBM as $BM): ?>
                <option value="<?php echo $BM->bm_empid; ?>"><?php echo $BM->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8 " id="employees">
    </div>
</div>

<div class="row" style="margin-top:1em;">
    <div class="result col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive" id="no-more-tables">
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
                    <?php
                    foreach ($allDoctors as $doctor) {
                        $total = Doctor:: profile_complete_percentage($doctor->docid);
                        ?>
                        <tr <?php
                        if (isset($_GET['complete'])) {
                            if ($total != 100) {
                                echo 'style ="display : none"';
                            }
                        }
                        ?>>
                            <td data-title="Name"><a href="SMviewAllProfiles.php?docid=<?php echo $doctor->docid ?>"><?php echo $doctor->name; ?></a></td>
                            <td data-title="Speciality"><?php echo $doctor->speciality; ?>&nbsp;</td>
                            <td data-title="Email"><?php echo $doctor->emailid; ?>&nbsp;</td>
                            <td data-title="Mobile"><?php echo $doctor->mobile; ?>&nbsp;</td>
                            <td data-title="Area"><?php echo $doctor->area; ?>&nbsp;</td>
                            <td data-title="Profiles Completed"><?php
                                echo ($total) . " %";
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
require_once("SMfooter.php");
