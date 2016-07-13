<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
require_once("../includes/class.visit_approval.php");

$bm_empid = $_SESSION['BM'];
$employees = Employee::find_by_bmid($bm_empid);
if (isset($_POST['submit'])) {
    //var_dump($_POST);
    $newVisitApproval = new VisitApproval();
    for ($i = 0; $i < count($_POST['status']); $i++) {
        $data = explode(":", $_POST['status'][$i]);
        //var_dump($data);
        $newVisitApproval->date = $data[0];
        $newVisitApproval->empid = $data[1];
        $newVisitApproval->status = 1;

        $approvalExist = $newVisitApproval->find_by_date_empid($newVisitApproval->date, $newVisitApproval->empid);
        if (!empty($approvalExist)) {
            $newVisitApproval->id = $approvalExist->id;
            $newVisitApproval->update();
        } else {
            if (isset($newVisitApproval->id)) {
                unset($newVisitApproval->id);
            }
            $newVisitApproval->create();
        }
    }
}
require_once("BMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Approve TM Visit</h1>
    </div>                <!-- /.col-lg-12 -->
</div>
<div class="row">
    <form action="#" method="post" id="form1">
        <div class="col-lg-4 center-block"> 
            <select class="form-control" id="month" onchange="isDate()" name="month">
                <option value="">SELECT MONTH</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month = $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                    if ($_POST['month'] == $m) {
                        echo '<option value="' . $m . '" selected >' . $month . '</option>';
                    } else {
                        echo '<option value="' . $m . '"  >' . $month . '</option>';
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
                    if ($_POST['year'] == date('Y', strtotime($years))) {
                        echo '<option value="' . date('Y', strtotime($years)) . '" selected>' . date('Y', strtotime($years)) . '</option>';
                    } else {
                        echo '<option value="' . date('Y', strtotime($years)) . '">' . date('Y', strtotime($years)) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

    </form>
</div>
<form action="" method="post">
    <div class="row row-margin-top">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover ">
                    <tr>
                        <th style="width: 10%">Date</th>
                        <?php
                        foreach ($employees as $employee) {
                            echo '<th >' . $employee->name . '</th>';
                        }
                        ?>
                    </tr>
                    <?php
                    if (isset($_POST['month']) && isset($_POST['year']) && trim($_POST['month']) != '') {
                        $year = $_POST['year'];
                        $month = $_POST['month'];
                        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $output = '';
                        for ($i = 1; $i <= $number_of_days; $i++) {
                            $date = $year . '-' . $month . '-' . $i;
                            ?>
                            <tr style="<?php
                            if (date("D", strtotime($date)) === 'Sun') {
                                echo 'background: #FCDCDC';
                            }
                            ?>">
                                <td>
                                    <div class="date">
                                        <span class="binds"></span>
                                        <span class="month"><?php echo date("M ", strtotime($date)) ?></span>
                                        <span class="day"><b><?php echo $i ?></b></span>
                                    </div>
                                </td>
                                <?php
                                $empCount = 1;
                                foreach ($employees as $employee) {
                                    $empCount++;
                                    $entryExist = Planning::find_by_date($_POST['month'], $employee->empid, $_POST['year']);
                                    if (!empty($entryExist)) {
                                        $planning = Planning::find_by_date_empid($date, $employee->empid);
                                        $visit_approval = new VisitApproval();
                                        $approvalExist = $visit_approval->find_by_date_empid($date, $employee->empid);
                                        ?>
                                        <td>
                                            <input type="checkbox" name ="status[]" value="<?php echo $date .":".$employee->empid; ?>" <?php
                                            if (!empty($approvalExist)) {
                                                echo 'checked';
                                            }
                                            ?>> Join
                                                   <?php
                                                   if (isset($planning->area)) {
                                                       echo "<label class ='label label-success'>" . $planning->area . '</label>';
                                                   }
                                                   ?>
                                        </td>
                                        <?php
                                    } else {
                                        echo '<td></td>';
                                    }
                                }
                                ?>
                            </tr>

                        <?php }
                        ?>
                        <tr>
                            <td colspan="<?php echo $empCount; ?>">
                                <input type="submit" name="submit" class="btn  btn-primary" value="Save">
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>                <!-- /.col-lg-12 -->
        </div>   
    </div>                <!-- /.col-lg-12 -->
</form>
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

    $(document).ready(function () {
        var checkflag = true;
        $('#checkAll').click(function () {

            if (checkflag == true) {
                $(' input:checkbox').attr('checked', true);
                checkflag = false;
            } else if (checkflag == false) {
                $(' input:checkbox').attr('checked', false);
                checkflag = true;
            }

        });
    });
</script>
<?php require_once("BMfooter.php"); ?>