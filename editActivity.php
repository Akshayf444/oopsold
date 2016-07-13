<?php
//session_start();
//if (!isset($_SESSION['employee'])) {
//    header("Location:login.php");
//}
require_once(dirname(__FILE__) . "/includes/initialize.php");
if (isset($_GET['act_id'])) {
    $act_id = $_GET['act_id'];
    $Activity = Activity::find_by_actid($act_id);
    $brand_activity = BrandActivity::find_by_act_id($act_id);

    function ProductList($id = "") {
        $Products = Product::find_all();
        $ProductList = '<option value ="" data-rate="0" > Select Brand</option> ';
        foreach ($Products as $Product) {
            if ($id == $Product->id) {
                $ProductList .='<option value ="' . $Product->id . '" data-rate="' . $Product->pts . '" selected>' . $Product->name . '</option>';
            } else {
                $ProductList .='<option value ="' . $Product->id . '" data-rate="' . $Product->pts . '" >' . $Product->name . '</option>';
            }
        }
        return $ProductList;
    }

}
?>
<div id="fullCalModal" class="modal">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 >Edit Activity</h4>
            </div>

            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <label>Rx GrandTotal</label>
            </div>
            <div  class="modal-body table-responsive">
                <form action ='viewActivityDetails.php?act_id=<?php echo $act_id; ?>' method="post">
                    
                    <table class="table table-bordered  " id="items" >
                        <tr>
                            <?php for ($i = 1; $i <= 8; $i++) { ?>
                                <th>
                                    <select style="
                                            padding: 0px;
                                            height: 20px;
                                            margin-bottom: 0.2em;
                                            font-size: 12px;
                                            " name="brandlist<?php echo $i; ?>" class="form-control brand<?php echo $i; ?>">
                                        <?php echo ProductList($brand_activity->{'brand' . $i}); ?>
                                    </select>
                                    No of units
                                </th>
                            <?php } ?>

                            <th>Total Business</th>
                        </tr>
                        <tr class="targetfields">
                            <?php for ($i = 1; $i < 9; $i++) { ?>
                                <td>
                                    <input type="text" name="brand<?php echo $i; ?>" value="<?php
                                        echo $Activity->{'brand' . $i};                                    
                                    ?>" class="form-control common" id="brand<?php echo $i . "1"; ?>"/>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="hidden" name="activity_type" value="<?php echo $Activity->activity_type; ?>">
                                <input type="hidden" name="activity_date" value="<?php echo $Activity->activity_date; ?>">
                                <input type="hidden" name="doctor_name" value="<?php echo $Activity->doc_id; ?>">
                                <input type="hidden" name="expances" value="<?php echo $Activity->expances; ?>">
                                <input type="hidden" name="highlight" value="<?php echo $Activity->highlight; ?>">
                                <input type="hidden" name="id" value="<?php echo $brand_activity->id; ?>">
                                <input type="hidden" name="filename" value="<?php echo $Activity->filename; ?>">
                                <input type="text" name="total" value="<?php echo $Activity->total?>" class="form-control subtotal" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9">
                                <input type="submit" name="edit" value="Save" class="btn btn-primary">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function () {
        jQuery("#items ").delegate('.common ', 'keyup', function () {

            var brand1 = jQuery("#brand11").val() * jQuery(".brand1").find(':selected').data('rate');
            var brand2 = jQuery("#brand21").val() * jQuery(".brand2").find(':selected').data('rate');
            var brand3 = jQuery("#brand31").val() * jQuery(".brand3").find(':selected').data('rate');
            var brand4 = jQuery("#brand41").val() * jQuery(".brand4").find(':selected').data('rate');
            var brand5 = jQuery("#brand51").val() * jQuery(".brand5").find(':selected').data('rate');
            var brand6 = jQuery("#brand61").val() * jQuery(".brand6").find(':selected').data('rate');
            var brand7 = jQuery("#brand71").val() * jQuery(".brand7").find(':selected').data('rate');
            var brand8 = jQuery("#brand81").val() * jQuery(".brand8").find(':selected').data('rate');

            var subtotal = parseFloat(brand8) + parseFloat(brand7) + parseFloat(brand6) + parseFloat(brand5) + parseFloat(brand4) + parseFloat(brand3) + parseFloat(brand2) + parseFloat(brand1);
            jQuery(".subtotal").val(subtotal.toFixed(2));

        });
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