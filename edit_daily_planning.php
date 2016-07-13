<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
require_once(dirname(__FILE__) . "/includes/class.activity_master.php");

function activityList($id) {
    $Activities = ActivityMaster::find_all();
    $output = '<option value ="0" >Select Activity</option>';
    foreach ($Activities as $Activity) {
        if ($Activity->id == $id) {
            $output .='<option value ="' . $Activity->id . '" selected>' . $Activity->activity . '</option>';
        } else {
            $output .='<option value ="' . $Activity->id . '" >' . $Activity->activity . '</option>';
        }
    }

    return $output;
}

if (isset($_POST['id'])) {
    $DailyPlanning = DailyCallPlanning::find_by_id($_POST['id']);
}
?>
<div id="fullCalModal" class="modal">
    <div class="modal-dialog" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 >Edit Daily Planning</h4>
            </div>
            <form action="" method="post">
                <div  class="modal-body">
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <th > Doctor Name   </th>
                                <th > MSL Code   </th>
                                <th > Clinic  </th>
                                <th > Product 1  </th>
                                <th > Product 2 </th>
                                <th > Product 3 </th>   
                                <th > Input </th>
                                <th > Service   </th>
                                
                                <th >POB Collected  </th>
                                <th >Post Call Planning</th>
                                <th style="width: 8%"> Meet </th>
                            </tr>
                        </thead>
                        <tbody class="result">

                            <?php
                            if (!empty($DailyPlanning)) {
                                $BasicProfile = BasicProfile::find_by_docid($DailyPlanning->docid);
                                $Doctor = Doctor::find_by_docid($DailyPlanning->docid);
                                ?>
                                <tr>
                                    <td><?php echo $Doctor->name ?></td>
                                    <td><?php echo isset($BasicProfile->msl_code) ? $BasicProfile->msl_code : "-" ?></td>
                                    <td><?php echo isset($BasicProfile->clinic_name) ? $BasicProfile->clinic_name : "-" ?></td>
                                    <td><?php
                                        $Product = Product::find_by_id($DailyPlanning->product1_id);
                                        echo $Product->name
                                        ?>
                                    </td>
                                    <td><?php
                                        $Product = Product::find_by_id($DailyPlanning->product2_id);
                                        echo $Product->name
                                        ?>
                                    </td>
                                    <td><?php
                                        $Product = Product::find_by_id($DailyPlanning->product3_id);
                                        echo $Product->name
                                        ?>
                                    </td>
                                    <td>
                                        <input type="hidden" name="product1_id" value="<?php echo $DailyPlanning->product1_id; ?>">
                                        <input type="hidden" name="product2_id" value="<?php echo $DailyPlanning->product2_id; ?>">
                                        <input type="hidden" name="product3_id" value="<?php echo $DailyPlanning->product3_id; ?>">

                                        <input type="hidden" name="id" value="<?php echo $DailyPlanning->id; ?>">
                                        <input type="hidden" name="docid" value="<?php echo $DailyPlanning->docid; ?>">
                                        <input type="hidden" name="plan_id" value="<?php echo $DailyPlanning->plan_id; ?>">
                                        <input type="text" class="form-control" name="inputs" value="<?php echo $DailyPlanning->input; ?>"></td>
                                    <td><input type="text" class="form-control" name="services" value="<?php echo $DailyPlanning->service; ?>"></td>

                                    <td><input type="text" class="form-control" name="pob" value="<?php echo $DailyPlanning->pob ?>" placeholder="Value in Rs."></td>
                                    <td><input type="text" class="form-control" name="post_call_planning" value="<?php echo $DailyPlanning->post_call_planning ?>"></td>
                                    <td>
                                        <select class="form-control" name="meet">
                                            <option value="Yes" <?php if ($DailyPlanning->meet === 'Yes'){ echo 'selected'; }?>>Yes</option>
                                            <option value="No" <?php if ($DailyPlanning->meet === 'No'){ echo 'selected'; }?>>No</option>
                                        </select>
                                    </td>                                
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td colspan="12">
                                    <input type="submit" name="editDailyPlan" class="btn btn-primary" value="Save">
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $("#specify").css("display", "none");
        // Add onclick handler to checkbox w/id checkme

        $('#activity').change(function () {
            if ($(this).val() == '12') {
                $('#specify').css({'display': 'block'});
            } else {
                $('#specify').css({'display': 'none'});
            }
        });

        if ($('#activity').val() == 0) {
            $('#specify').css({'display': 'block'});
        }
    });
</script>
<script>
    $(document).ready(function () {
        var isIE = navigator.userAgent.indexOf(' MSIE ') > -1;
        if (isIE) {
            $('#BookAppointment').removeClass('fade');
        }
        $("#fullCalModal").modal();
    });
</script>

