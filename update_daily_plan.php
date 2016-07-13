<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
//$AreaList = Doctor::areaList($_SESSION['employee']);
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $AreaList = Planning::find_by_id($id);
    $alreadyVisited = DoctorVisit::find_by_plan_id($id);
    $DailyCallPlanExist = DailyCallPlanning::find_by_plan_id($id);

    if (!empty($DailyCallPlanExist)) {
        echo '<script>window.location = "Daily_plan.php?plan_id=' . $id . '";</script>';
        exit();
    } elseif ($alreadyVisited != FALSE) {
        $_SESSION['visit_ids'] = array();
        foreach ($alreadyVisited as $value) {
            array_push($_SESSION['visit_ids'], $value->id);
        }
        echo '<script>window.location= "Daily_plan.php?plan_id='. $id .'"</script>';
        exit();
    }

    $finalAreaList = array();
    if (!empty($AreaList)) {
        foreach (explode(",", $AreaList->area) as $value) {
            array_push($finalAreaList, $value);
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

    //var_dump($finalProcessed);

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
?>
<div id="fullCalModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 >Add Daily Planning</h4>
            </div>
            <form action="" method="post">
                <div  class="modal-body">
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
                        <input type ="hidden" name="plan_id" value="<?php echo $id; ?>" >
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
    </div>
</div>
<script>
    $(document).ready(function () {
        var isIE = navigator.userAgent.indexOf(' MSIE ') > -1;
        if (isIE) {
            $('#BookAppointment').removeClass('fade');
        }
        $("#fullCalModal").modal();
    });
</script>