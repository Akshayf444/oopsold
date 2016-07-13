<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$final = 0;
if (isset($_POST['buisness'])) {
    $buisness = $_POST['buisness'];
    $employees = Employee::find_by_buisness($buisness);
    $SMs = SM::find();
} else {
    $employees = Employee::find_all();
    $SMs = SM::find();
}

$zonelist = '';

function ZoneList($id = "") {
    $Zones = Employee::find_zone();
    if (!empty($Zones)) {
        foreach ($Zones as $Zone) {
            if ($Zone->Zone == $id) {
                echo "<option value = '" . $Zone->Zone . "' selected >" . $Zone->Zone . "</option>";
            } else {
                echo "<option value = '" . $Zone->Zone . "' >" . $Zone->Zone . "</option>";
            }
        }
    }
}

$pageTitle = "TM Report";
require_once("adminheader.php");
?>

<script>
    function Search() {

        var search_term = $(".employee").val();
        $.post('getTMReport.php', {search_term: search_term}, function (data) {
            $('#result').html(data);
        });

        $.post('getState.php', {search_term: search_term}, function (data) {
            $('#state').html(data);
        });
    }

</script>
<script>
    function Search1() {
        var search_term = $(".selectState").val();

        $.post('getStateWiseTMReport.php', {search_term: search_term}, function (data) {
            $('#result').html(data);
        });

        $.post('getRegion.php', {search_term: search_term}, function (data) {
            $('#region').html(data);
        });
    }
</script>
<script>
    function Search2() {

        var search_term = $(".selectRegion").val();
        $.post('getRegionWiseTMReport.php', {search_term: search_term}, function (data) {

            $('#result').html(data);
        });
    }
</script>
<script>
    function Search3() {
        var search_term = $(".sm").val();
        $.post('getSMWiseTMReport.php', {search_term: search_term}, function (data) {
            $('#result').html(data);
        });
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">TM Report</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row" >
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
        Zone:
        <select  onchange="Search()" class="employee form-control">
            <option value="">Select Zone</option>
            <?php ZoneList(); ?>
        </select>
    </div>

    <div id="state" class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
        State:
        <select  onchange="Search1()" class="selectState form-control" >
            <option value="">Select State</option>
        </select>
    </div>

    <div id="region" class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
        Region:
        <select  onchange="Search2()" class="selectRegion form-control" >
            <option value="">Select Region</option>
        </select>
    </div>

    <div id="sm" class="col-lg-3 col-sm-3 col-md-3 col-xs-3" >
        SM:
        <select  onchange="Search3()" class="sm form-control">
            <option value="">Select SM</option>
            <?php foreach ($SMs as $SM): ?>
                <option value="<?php echo $SM->sm_empid; ?>"><?php echo $SM->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="row col-lg-3 col-sm-3 col-md-3 col-xs-3"  style="margin-top:1em;margin-bottom:1em">
    <form action="TMreport.php" method="post">
        Buisness Less:<input type="text" name="buisness" class="form-control"  >
    </form>
</div>

<div class="row" style="margin-top:1em;">
    <div id="result"  class="table-responsive col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <table class="table table-bordered table-hover ">
            <tr>
                <th>BM-Id</th>
                <th>Emp-Id</th>
                <th>Emp Name</th>
                <th>No of Doctors</th>
                <th>No of Completed Profiles</th>
                <th>Completed Basic Profiles</th>
                <th>Completed Academic Profiles</th>
                <th>Completed Service Profiles</th>
            </tr>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo $employee->bm_empid; ?></td>
                    <td><?php echo $employee->empid; ?></td>
                    <td><?php echo $employee->name; ?></td>
                    <td><?php echo $doctorCount = Doctor::count_all($employee->empid); ?></td>
                    <td><?php
                        $total = Doctor::profileCount_empwise($employee->empid);

                        echo $total;
                        $total = 0;
                        ?>
                    </td>
                    <td><?php echo Employee::count_basic_profile($employee->empid); ?></td>
                    <td><?php echo Employee::count_service($employee->empid); ?></td>
                    <td><?php echo Employee::count_academic_profile($employee->empid); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php require_once("adminfooter.php"); ?>