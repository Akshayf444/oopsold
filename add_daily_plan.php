<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$AreaList = Doctor::areaList($_SESSION['employee']);
if (isset($_POST['month']) && isset($_POST['year'])) {
    $year = $_POST['year'];
    $month = $_POST['month'];
    $AreaList = Planning::find_by_date($month, $empid, $year);

    $finalAreaList = array();
    if (!empty($AreaList)) {
        foreach ($AreaList as $Area) {
            foreach (explode(",", $Area->area) as $value) {
                array_push($finalAreaList, $value);
            }
        }
    }
    $finalProcessed = array();
    foreach (array_unique($finalAreaList) as $value) {
        $Doctors = Doctor::find_doctor_by_area($value, $empid);
        if (!empty($Doctors)) {
            foreach ($Doctors as $Doctor) {
                array_push($finalProcessed, $Doctor);
            }
        }
    }

    $output = '';
    foreach ($finalProcessed as $Doctor) {
        $output .= '<tr>
                        <td>
                            ' . $Doctor->name . '
                        </td>
                        <td>
                        <input type="checkbox"  name ="doctors[]" value = "' . $Doctor->docid . '" ></input>
                        
                        </td>
                    </tr>';
    }

    if (!empty($finalProcessed)) {
        $output .='<tr> <td colspan="2"> <input type="submit" class="btn btn-primary" value="Save" name="submit"> </td></tr>';
    }
}

if (isset($_POST['submit']) && isset($_POST['doctors'])) {
    $_SESSION['visit_ids'] = array();
    $trimmed_array = array_filter(array_map('trim', $_POST['doctors']));
    $newVisit = new DoctorVisit();
    foreach ($trimmed_array as $docid) {
        $newVisit->docid = $docid;
        $newVisit->empid = $_SESSION['employee'];
        $newVisit->visit_date = date('Y-m-d', time());
        $result = $newVisit->create();
        array_push($_SESSION['visit_ids'], $result);
    }
    redirect_to('view_daily_plan.php');
}
require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Daily Planning</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <form action="#" method="post" id="form1">


        <div class="col-lg-4 center-block"> 
            <select class="form-control" id="month" onchange="isDate()" name="month">
                <option value="">SELECT MONTH</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month = date('F', mktime(0, 0, 0, $m));
                    echo '<option value="' . $m . '">' . $month . '</option>';
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
                    echo '<option value="' . date('Y', strtotime($years)) . '">' . date('Y', strtotime($years)) . '</option>';
                }
                ?>
            </select>
        </div>
    </form>
</div>
<div class="row row-margin-top">
    <form action="" method="post">
        <div class="col-lg-12 " >
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th >
                            Doctor Name
                        </th>
                        <th >
                            Select Doctor
                        </th>
                    </tr>
                </thead>
                <tbody class="result">
                    <?php
                    if (isset($output)) {
                        echo $output;
                    }
                    ?>
                </tbody>

            </table>
        </div>
    </form>
</div>
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