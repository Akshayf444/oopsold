<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:login.php");
}
require_once("../includes/initialize.php");
$act_id = $_POST['act_id'];
$Activity = Activity::find_by_actid($act_id);
$brand_activity = BrandActivity::find_by_act_id($act_id);
?>
<div id="fullCalModal" class="modal">
    <div class="modal-dialog" style="width: 1150px;height: 60%">
        <div class="modal-content">
            <div class="modal-header btn-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><?php
                    $ActName = ActivityMaster::find_by_id($Activity->activity_type);
                    echo isset($ActName->activity) ? $ActName->activity : $Activity->activity_type;
                    ?>
                </h3>
            </div>
            <div  class="modal-body">

                <div class="row ">
                    <div class="col-lg-6">
                        <dl class="dl-horizontal">
                            <dt>Activity Date</dt>
                            <?php echo '<dd>' . date('d-m-Y ', strtotime($Activity->activity_date)) . '</dd>'; ?>
                        </dl>
                    </div>
                    <div class="col-lg-6">
                        <dl class="dl-horizontal">
                            <dt>Doctor Name</dt>
                            <?php
                            if (is_int($Activity->doc_id)) {
                                $doctor_name = Doctor::find_by_docid($Activity->doc_id);
                            }

                            echo isset($doctor_name->name) ? '<dd>' . $doctor_name->name . '</dd>' : '';
                            ?>
                        </dl>
                    </div>
                </div>
                <div class="row row-margin-top">
                    <div class="col-lg-12">
                        <label>Rx Grand Total</label>
                        <hr>
                        <table class="table table-bordered table-hover " id="items" >
                            <tr>
                                <?php for ($i = 1; $i <= 8; $i++) { ?>

                                    <?php
                                    $Product = Product::find_by_id($brand_activity->{'brand' . $i});
                                    if (isset($Product->name))
                                        echo "<th>" . $Product->name . "</th>"
                                        ?>



                                <?php } ?>

                                <th>Total Business</th>
                            </tr>
                            <tr class="targetfields">
                                <?php
                                $finalTotal = 0;
                                for ($i = 1; $i < 9; $i++) {
                                    $Product = Product::find_by_id($brand_activity->{'brand' . $i});
                                    if (isset($Product->name)) {
                                        ?>
                                        <td>
                                            <?php
                                            echo $Activity->{'brand' . $i};
                                            $finalTotal += $Activity->{'brand' . $i};
                                            ?>
                                        </td>
                                        <?php
                                    }
                                }

                                echo '<td>' . $Activity->total . '</td>';
                                ?>

                            </tr>
                        </table>
                    </div>      <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <label>Highlights Of Activity</label>
                        <hr>
                        <p><?php echo $Activity->highlight; ?></p>
                    </div>      <!-- /.col-lg-12 -->
                </div>
            </div>

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
