<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");

$zonelist = '';
$statelist = '';

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

if (isset($_POST['zone'])) {
    $zone = $_POST['zone'];
    $employees = Employee::find_all_by_zone($zone);
    $states = Employee::find_state($zone);
} elseif (isset($_POST['state'])) {
    $state = $_POST['state'];
    $employees = Employee::find_all_by_state($state);
    $regions = Employee::find_region($state);
} elseif (isset($_POST['region'])) {
    $region = $_POST['region'];
    $employees = Employee::find_all_by_region($region);
} else {
    $page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
    $per_page = 50;
    $total_count = Employee::count();
    $pagination = new Pagination($page, $per_page, $total_count);
    $sql = "SELECT * FROM employees ";
    $sql .= "LIMIT {$per_page} ";
    $sql .= "OFFSET {$pagination->offset()}";
    $employees = Employee::find_by_sql($sql);
}

if (isset($_POST['lockAllBasic'])) {
    $employees = Employee::find_all();
    $lockbasic = new Employee();
    foreach ($employees as $employee) {
        $lockbasic->lockBasic($employee->empid, 1);
    }
    redirect_to("lockManager.php");
}
if (isset($_POST['lockAllBuisness'])) {
    $lockbasic = new Employee();
    $employees = Employee::find_all();
    foreach ($employees as $employee) {
        $lockbasic->lockBuisness($employee->empid, 1);
    }
    redirect_to("lockManager.php");
}
if (isset($_POST['lockAllAcademic'])) {
    $employees = Employee::find_all();
    $lockbasic = new Employee();
    foreach ($employees as $employee) {
        $lockbasic->lockAcademic($employee->empid, 1);
    }
    redirect_to("lockManager.php");
}
if (isset($_POST['lockAllService'])) {
    $employees = Employee::find_all();
    $lockbasic = new Employee();
    foreach ($employees as $employee) {
        $lockbasic->lockService($employee->empid, 1);
    }
    redirect_to("lockManager.php");
}
if (isset($_POST['unlockAllBasic'])) {
    $employees = Employee::find_all();
    $lockbasic = new Employee();
    foreach ($employees as $employee) {
        $lockbasic->lockBasic($employee->empid, 0);
    }
    redirect_to("lockManager.php");
}
if (isset($_POST['unlockAllBuisness'])) {
    $employees = Employee::find_all();
    $lockbasic = new Employee();
    foreach ($employees as $employee) {
        $lockbasic->lockBuisness($employee->empid, 0);
    }
    redirect_to("lockManager.php");
}
if (isset($_POST['unlockAllAcademic'])) {
    $employees = Employee::find_all();
    $lockbasic = new Employee();
    foreach ($employees as $employee) {
        $lockbasic->lockAcademic($employee->empid, 0);
    }
    redirect_to("lockManager.php");
}
if (isset($_POST['unlockAllService'])) {
    $employees = Employee::find_all();
    $lockbasic = new Employee();
    foreach ($employees as $employee) {
        $lockbasic->lockService($employee->empid, 0);
    }
    redirect_to("lockManager.php");
}

$pageTitle = "Lock Manager";
require_once("adminheader.php");
?>

<script>
    function Search() {
        $('#animation').show();
        var search_term = $(".employee").val();
        $.post('getState.php', {search_term: search_term}, function (data) {
            $('#animation').hide();
            $('#state').html(data);
        });
    }

</script>
<script>
    function Search1() {
        var search_term = $(".selectState").val();
        $('#animation').show();
        $.post('getRegion.php', {search_term: search_term}, function (data) {
            $('#animation').hide();
            $('#region').html(data);
        });
    }
</script>
<script>
//For locking and Unlocking of profiles
    $(document).ready(function () {
        $(":checkbox").click(function () {
            var search_term = this.id;
            if ($(this).prop("checked")) {
                //Send request to lock page
                
                $.post('lockProfiles.php', {search_term: search_term}, function (data) {
                    $("#result").css("background", "#fff");
                });

            } else {
                //Send request to unlock page

                $.post('unlockProfiles.php', {search_term: search_term}, function (data) {
                    $("#result").css("background", "#fff");
                });

            }
        });
    });
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Lock Manager</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div  class="row">

    <div  class="col-lg-3 col-sm-3 col-md-3 col-xs-6">
        <form action="lockManager.php" method="post">
            Zone:
            <select  onchange="this.form.submit()" name="zone" class="form-control">
                <option value="">Select Zone</option>
                <?php
                if (isset($_POST['zone'])) {
                    ZoneList($_POST['zone']);
                } else {
                    ZoneList();
                }
                ?>
            </select>
        </form>
    </div>

    <div   class="col-lg-3 col-sm-3 col-md-3 col-xs-6">
        <form action="lockManager.php" method="post">
            State:
            <select  onchange="this.form.submit()" name="state" class="form-control">
                <option value="">Select State</option>
                <?php
                if (isset($states) && !empty($states)) {
                    foreach ($states as $state):
                        ?>
                        <option value="<?php echo $state->state; ?>" <?php
                        if (isset($_POST['state']) && ($_POST['state'] == $state->state)) {
                            echo "selected";
                        }
                        ?> ><?php echo $state->state; ?></option>
                                <?php
                            endforeach;
                        }
                        ?>
            </select>
        </form>
    </div>
    <div class="col-lg-3 col-sm-3 col-md-3 col-xs-6"  >
        <form action="lockManager.php" method="post">
            Region:
            <select  onchange="this.form.submit()" name="region" class="form-control">
                <option value="">Select Region</option>
                <?php
                if (isset($regions) && !empty($regions)) {
                    foreach ($regions as $region):
                        ?>
                        <option value="<?php echo $region->region; ?>"><?php echo $region->region; ?></option>
                        <?php
                    endforeach;
                }
                ?>
            </select>
        </form>
    </div>
</div>

<div  class="row " style="margin-top:1em;">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8" >
        <form action ="lockManager.php" method="post">
            <input type="submit" name="lockAllBasic" value="Lock All Basic" class="btn btn-primary btn-xs">
        </form>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8"  >
        <form action ="lockManager.php" method="post">
            <input type="submit" name="lockAllBuisness" value="Lock All Buisness" class="btn btn-primary btn-xs">
        </form>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8" >
        <form action ="lockManager.php" method="post">
            <input type="submit" name="lockAllService" value="Lock All Services" class="btn btn-primary btn-xs">
        </form>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8" > 
        <form action ="lockManager.php" method="post">
            <input type="submit" name="lockAllAcademic" value="Lock All Academic" class="btn btn-primary btn-xs">
        </form>
    </div>
</div>
<div  class="row" style="margin-top:1em;">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8" > 
        <form action ="lockManager.php" method="post">
            <input type="submit" name="unlockAllBasic" value="UN-Lock All Basic" class="btn btn-primary btn-xs">
        </form>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8" > 
        <form action ="lockManager.php" method="post">
            <input type="submit" name="unlockAllBuisness" value="UN-Lock All Buisness" class="btn btn-primary btn-xs">
        </form>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8" > 
        <form action ="lockManager.php" method="post">
            <input type="submit" name="unlockAllService" value="UN-Lock All Services" class="btn btn-primary btn-xs">
        </form>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8" > 
        <form action ="lockManager.php" method="post">
            <input type="submit" name="unlockAllAcademic" value="UN-Lock All Academic" class="btn btn-primary btn-xs">
        </form>
    </div>
</div>
<div class="row" style="margin-top:1em;margin-bottom: 1em" id="result">
    <div  class="col-lg-12 col-md-12 col-sm-12  col-xs-12 table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>SM-Id</th>
                <th>SM-Name</th>
                <th>BM-Id</th>
                <th>BM-Name</th>
                <th>Emp-Id</th>
                <th>Emp Name</th>
                <th>Lock Basic Profile</th>
                <th>Lock Service Profile</th>
                <th>Lock Business Profile</th>
                <th>Lock academic Profile</th>
            </tr>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php
                        $bmName = BM::find_by_bmid($employee->bm_empid);
                        $smName = isset($bmName->sm_empid) ? SM::find_by_smid($bmName->sm_empid) : "";
                        echo isset($bmName->sm_empid) ? $bmName->sm_empid : "-";
                        ?>
                    </td>
                    <td><?php echo isset($smName->name) ? $smName->name : "-"; ?></td>
                    <td><?php echo isset($employee->bm_empid) ? $employee->bm_empid : "-"; ?></td>
                    <td><?php echo isset($bmName->name) ? $bmName->name : "-"; ?></td>
                    <td id="<?php echo $employee->empid; ?>"><?php echo $employee->empid; ?></td>
                    <td><?php echo $employee->name; ?></td>
                    <td><input type="checkbox" name="lockbasic" 
                        <?php
                        if ($employee->lock_basic == 1) {
                            echo "checked";
                        }
                        ?> id="<?php echo $employee->empid . '_basic'; ?>" >
                    </td>
                    <td><input type="checkbox" name="lockservice" 
                        <?php
                        if ($employee->lock_service == 1) {
                            echo "checked";
                        }
                        ?> id="<?php echo $employee->empid . '_service'; ?>">
                    </td>
                    <td><input type="checkbox" name="lockbuisness"
                        <?php
                        if ($employee->lock_buisness == 1) {
                            echo "checked";
                        }
                        ?> id="<?php echo $employee->empid . '_buisness'; ?>">
                    </td>
                    <td><input type="checkbox" name="lockacademic" 
                        <?php
                        if ($employee->lock_academic == 1) {
                            echo "checked";
                        }
                        ?> id="<?php echo $employee->empid . '_academic'; ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class ="row">
        <div class="col-lg-12" style="clear: both;">
            <?php
            if ($pagination->total_pages() > 1) {

                echo '<div class="col-lg-10" style = "text-align:center">';
                for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                    ?>
                    <input type="button" class="btn btn-xs btn-red"  value="<?php echo $i; ?>" onclick="sendPaginationRequest(this.value)">
                    <?php
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
    function sendPaginationRequest(id) {
        var page = id;
        $.ajax({
            //Send request
            type: 'GET',
            data: {page: page},
            url: 'lockManagerPagination.php',
            success: function (data) {
                $("#result").html(data);
            }
        });
    }
</script>
<?php require_once("adminfooter.php"); ?>