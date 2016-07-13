<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$AreaList = Doctor::areaList($empid);

if (isset($_POST['submit'])) {
    $addPlanning = new Planning();
    for ($i = 0; $i < count($_POST['date']); $i++) {
        $addPlanning->id = 0;

        $addPlanning->date = $_POST['date'][$i];
        $addPlanning->status = $_POST['status'][$i];
        $addPlanning->remark = $_POST['remark'][$i];

        if (isset($_POST[$addPlanning->date])) {
            $addPlanning->area = implode(",", $_POST[$addPlanning->date]);
            $addPlanning->empid = $_SESSION['employee'];
            $entryExist = Planning::entryExist($addPlanning->date, $addPlanning->empid);
            if (!$entryExist && trim($addPlanning->area != '')) {
                $addPlanning->create();
            } else {
                $addPlanning->id = $entryExist->id;
                $addPlanning->update();
            }
        }
    }

    redirect_to("view_plan_monthly.php");
}
require_once("layouts/TMheader.php");
?>
<link href="css/chosen.min.css" rel="stylesheet" type="text/css"/>
<script src="js/chosen.jquery.min.js" type="text/javascript"></script>
<script src="js/chosen.proto.js" type="text/javascript"></script>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Monthly Planning</h1>
    </div>      <!-- /.col-lg-12 -->
</div>

<div class="row">
    <form action="#" method="post" id="form1">


        <div class="col-lg-4 center-block"> 
            <select class="form-control" id="month" onchange="isDate()" name="month">
                <option value="">SELECT MONTH</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month = $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                    if (isset($_POST['month']) && $_POST['month'] == $m) {
                        echo '<option value="' . $m . '"  selected >' . $month . '</option>';
                    } else {
                        echo '<option value="' . $m . '"   >' . $month . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-lg-4 center-block"> 
            <select class="form-control" onchange="isDate()" id="year" name="year" >
                <option value="">SELECT YEAR</option>
                <?php
                for ($i = 0; $i < 10; $i++) {
                    $years = '+' . $i . 'years';
                    if (isset($_POST['year']) && $_POST['year'] == date('Y', strtotime($years))) {
                        echo '<option value="' . date('Y', strtotime($years)) . '" selected >' . date('Y', strtotime($years)) . '</option>';
                    } else {
                        echo '<option value="' . date('Y', strtotime($years)) . '">' . date('Y', strtotime($years)) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </form>
</div>
<div class="row row-margin-top">
    <form action="" method="post">
        <div class="col-lg-12 " style="height: 900px;overflow-y: scroll">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th style="width: 10%">
                            Date
                        </th>
                        <th>Status</th>

                        <th >
                            Select Area
                        </th>
                        <th>Remark</th>

                    </tr>
                </thead>
                <tbody class="result">
                    <?php
                    if (isset($_POST['month']) && isset($_POST['year']) && trim($_POST['month']) != '') {

                        $year = $_POST['year'];
                        $month = $_POST['month'];
                        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $output = '';
                        for ($i = 1; $i <= $number_of_days; $i++) {
                            $date = $year . '-' . $month . '-' . $i;
                            $entryExist = Planning::entryExist($date, $empid);
                            ?>
                            <tr>
                                <td style="<?php
                                if (date("D", strtotime($date)) === 'Sun') {
                                    echo 'background: #FCDCDC';
                                }
                                ?>">
                                    <input type="hidden" class="calendar" value="<?php echo date("Y-m-d", strtotime($date)) ?>" name="date[]">
                                    <div class="date">
                                        <span class="binds"></span>
                                        <span class="month"><?php echo date("M ", strtotime($date)) ?></span>
                                        <span class="day"><b><?php echo $i ?></b></span>
                                    </div>
                                </td>
                                <td>
                                    <select class="form-control" name="status[]">
                                        <option value="0" <?php
                                        if (isset($entryExist->status) && $entryExist->status == '0') {
                                            echo 'selected';
                                        }
                                        ?>>Working</option>
                                        <option value="1" <?php
                                        if (isset($entryExist->status) && $entryExist->status == '1') {
                                            echo 'selected';
                                        }
                                        ?>>Camp/Activity</option>
                                        <option value="2" <?php
                                        if (isset($entryExist->status) && $entryExist->status == '2') {
                                            echo 'selected';
                                        }
                                        ?>>Meeting</option>
                                        <option value="3" <?php
                                        if (isset($entryExist->status) && $entryExist->status == '3') {
                                            echo 'selected';
                                        }
                                        ?>>Holiday</option>
                                        <option value="4" <?php
                                        if (isset($entryExist->status) && $entryExist->status == '4') {
                                            echo 'selected';
                                        }
                                        ?>>Leave</option>
                                    </select>
                                </td>

                                <td style="<?php
                                if (date("D", strtotime($date)) === 'Sun') {
                                    echo 'background: #FCDCDC';
                                }
                                ?>">
                                    <select data-placeholder="Choose Area" class="chosen-select" multiple style="width:350px;" tabindex="4" name="<?php echo date("Y-m-d", strtotime($date)) ?>[]">
                                        <option value=""></option>
                                        <?php
                                        //$areaList = explode(",", $entryExist->area);
                                        foreach (array_unique(json_decode($AreaList)) as $area) {
                                            if (!empty($entryExist)) {
                                                if (in_array($area, explode(",", $entryExist->area))) {
                                                    echo '<option value="' . $area . '"  selected >' . $area . '</option>';
                                                }
                                            } else {
                                                echo '<option value="' . $area . '">' . $area . '</option>';
                                            }
                                        }
                                        ?>

                                    </select>

                                </td>
                                <td>
                                    <textarea class="form-control" name="remark[]"><?php
                                        if (isset($entryExist->remark)) {
                                            echo $entryExist->remark;
                                        }
                                        ?></textarea>
                                </td>
                            </tr>
                            <?php
                        }
                        echo '<tr> <td colspan="4"> <input type="submit" class="btn btn-primary" value="Save" name="submit"> </td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </form>
</div>
<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>

<?php require_once("layouts/TMfooter.php"); ?>
<script>
    function isDate() {
        var month = $("#month").val();
        var year = $("#year").val();

        var today = new Date();
        var mm = today.getMonth() + 1; //January is 0!

        if (mm < 10) {
            mm = '0' + mm;
        }
        if (month < 10) {
            month = '0' + month;
        }
        var today = Date.parse(today.getFullYear() + '-' + mm + '-' + 1);
        var enteredDate = Date.parse(year + '-' + month + '-' + 1);

        if (year != '') {
            if (enteredDate < today) {
                alert("Cannot Select Past Date");
            } else {
                $("#form1").submit();
            }
        }
    }
</script>
<!--<script type="text/javascript">
    $(function () {
        var sampleTags = <?php //echo $AreaList;             ?>;
        $('.areaList').tagit({
            availableTags: sampleTags,
            allowSpaces: true,
            removeConfirmation: true
        });
    });
</script>-->
<script type="text/javascript">
    var config = {
        '.chosen-select': {},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "95%"}
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
</script>