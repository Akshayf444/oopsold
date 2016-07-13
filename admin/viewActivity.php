<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:logout.php");
}
require_once("../includes/initialize.php");
$pageTitle = "View Activity Details";
require_once("../includes/class.activity_master.php");

$activity_count = 0;
$output = '';
$tmlist = '';
$bmlist = '';
$smlist = '';

function SMList($id = "") {
    $Employees = SM::find();
    $smlist .= '<option value ="0" >Select SM</option>';
    foreach ($Employees as $Employee) {
        if ($Employee->empid == $id) {
            $smlist .='<option value ="' . $Employee->sm_empid . '" selected >' . $Employee->name . '</option>';
        } else {
            $smlist .='<option value ="' . $Employee->sm_empid . '" >' . $Employee->name . '</option>';
        }
    }
    return $smlist;
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

if (isset($_POST['sm_empid'])) {
    $sm_empid = $_POST['sm_empid'];
    $conditions = array(' INNER JOIN `employees` e ON e.`empid`= act.empid ', 'INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` ', 'INNER JOIN sm  ON sm.`sm_empid` = bm.`sm_empid` WHERE sm.`sm_empid` = ' . $sm_empid);
    $sql = $sql = Activity::buildQuery($conditions);
    $Activitys = QueryWrapper::executeQuery($sql);
} elseif (isset($_POST['bm_empid'])) {
    $bm_empid = $_POST['bm_empid'];
    $conditions = array(' INNER JOIN `employees` e ON e.`empid`= act.empid ', 'INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` WHERE bm.`bm_empid` = ' . $bm_empid);
    $sql = Activity::buildQuery($conditions);
    $Activitys = QueryWrapper::executeQuery($sql);
} elseif (isset($_POST['empid'])) {
    $empid = $_POST['empid'];
    $conditions = array(' INNER JOIN `employees` e ON e.`empid`= act.empid AND e.empid = ' . $empid);
    $sql = Activity::buildQuery($conditions);
    $Activitys = QueryWrapper::executeQuery($sql);
    $activity_count = Activity::count_by_empid($empid);
} else {
    $Activitys = Activity::getDetails();
    $activity_count = Activity::count_all();
}
require_once("adminheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">View Activity Details</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<style>
    .modal-backdrop.in{
        opacity: 0.2;
    }
</style>
<script>
    function Zone() {

        var search_term = $(".zone").val();
        $.post('filters.php', {zone: search_term, zonewise_activity: 'true'}, function (data) {
            $('#result').html(data);
        });

        $.post('getState.php', {search_term: search_term}, function (data) {
            $('#state').html(data);
        });
    }

    function Search1() {
        var search_term = $(".selectState").val();
        var zone = $(".zone").val();
        $.post('filters.php', {state: search_term, zonewise_activity: 'true', zone: zone}, function (data) {
            $('#result').html(data);
        });

        $.post('getRegion.php', {search_term: search_term}, function (data) {
            $('#region').html(data);
        });
    }

    function Search2() {
        var search_term = $(".selectRegion").val();
        var state = $(".selectState").val();
        var zone = $(".zone").val();
        $.post('filters.php', {region: search_term, zonewise_activity: 'true', zone: zone, state: state}, function (data) {
            $('#result').html(data);
        });
    }
</script>
<div class="row" >
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-3">

        <select  onchange="Zone()" class="zone form-control">
            <option value="">Select Zone</option>
            <?php ZoneList(); ?>
        </select>
    </div>

    <div id="state" class="col-lg-3 col-sm-3 col-md-3 col-xs-3">

        <select  onchange="State()" class="selectState form-control" >
            <option value="">Select State</option>
        </select>
    </div>

    <div id="region" class="col-lg-3 col-sm-3 col-md-3 col-xs-3">

        <select  onchange="Region()" class="selectRegion form-control" >
            <option value="">Select Region</option>
        </select>
    </div>


</div>
<div class="row row-margin-top">
    <form action="" method="post">
        <div class="col-lg-3">
            <select name="sm_empid" class="form-control " onchange="this.form.submit()" required >
                <?php
                if (isset($_POST['sm_empid'])) {
                    echo SMList($_POST['sm_empid']);
                } else {
                    echo SMList();
                }
                ?>
            </select>
        </div>
        <div class="col-lg-3">
            <select name="bm_empid" class="form-control " onchange="this.form.submit()" required >
                <option>Select BM</option>
            </select>
        </div>
        <div class="col-lg-3">
            <select name="empid" class="form-control " onchange="this.form.submit()" required id="empid">
                <option>Select TM</option>
            </select>
        </div> 

    </form>
</div>
<div class="row row-margin-top" id="result">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive">
        <table class="table table-bordered table-hover " id="items" >
            <tr>
                <th>Activity Type</th>
                <th>Activity Date</th>
                <th>Doctor Name</th>
                <th>MSL Code</th>
                <th>Expenses</th>
                <th>Total Business</th>
            </tr>
            <?php
            $page_count = 1;
            if (!empty($Activitys)) {
                foreach ($Activitys as $Activity) {
                    ?>
                    <tr>
                        <td><?php
                            echo isset($Activity->master_type) ? $Activity->master_type : $Activity->activity_type;
                            ?>
                        </td>
                        <td><?php echo date('d-m-Y', strtotime($Activity->activity_date)); ?></td>
                        <td><?php
                            echo $Activity->name;
                            ?>
                        </td>
                        <td><?php
                            echo (isset($Activity->msl_code)) ? $Activity->msl_code : "-"
                            ?>
                        </td>
                        <td><?php echo $Activity->expances; ?></td>
                        <td>
                            <?php
                            echo $Activity->total;
                            ?>
                        </td>
                        <td><a id="<?php echo $Activity->act_id; ?>" href="#"  class="btn btn-success btn-xs " >View Details</a></td>
                    </tr>
                    <?php
                    $page_count++;
                }
            }
            ?>
        </table>
    </div>
</div>
<div id="modalpopup"></div>
<script>
    $('.btn').click(function () {
        var id = $(this).attr('id');
        $.ajax({
            type: 'POST',
            data: {act_id: id},
            url: 'viewActivityDetails.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    });

</script>
<?php require_once("adminfooter.php"); ?>