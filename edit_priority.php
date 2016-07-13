<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");

function ProductList($id) {
    $ProductList = '<option>Select Product</option>';
    $Products = Product::find_all();
    foreach ($Products as $Product) {
        if ($id == $Product->id) {
            $ProductList .='<option value ="' . $Product->id . '"  selected>' . $Product->name . '</option>';
        } else {
            $ProductList .='<option value ="' . $Product->id . '" >' . $Product->name . '</option>';
        }
    }
    return $ProductList;
}

if (isset($_POST['id'])) {
    $Product = PriorityProduct::find_by_id($_POST['id']);
    $doctor = Doctor::find_by_docid($Product->docid);
    $BasicProfile = BasicProfile::find_by_docid($Product->docid);
    $lastMonthBusiness = BusiProfile::find_by_docid($doctor->docid);
    $lastMonthBusiness = array_shift($lastMonthBusiness);
}
?>
<div id="fullCalModal" class="modal">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 >Edit Priority Product</h4>
            </div>

            <div class="modal-body">
                <form action="" method="post" >
                    <table class="table table-bordered table-hover " id="no-more-tables">
                        <thead>
                        <tr>
                            <th>Doctor Name</th><th>MSL Code</th><th>Area</th><th>Doctor Class</th><th>Priority Product 1</th><th>Priority Product 2</th>
                            <th>Priority Product 3</th>
                            <th>Total Business</th>
                        </tr>
                        </thead>
                        <tr>
                            <td data-title="Doctor Name">
                                <input type="hidden" name="prt_id[]" value="<?php echo $Product->id; ?>">
                                <input type="hidden" name="docid[]" value="<?php echo $Product->docid; ?>">
                                <?php echo $doctor->name; ?>
                            </td>
                            <td data-title="MSL Code"><?php echo isset($BasicProfile->msl_code) ? $BasicProfile->msl_code : "-"; ?></td>
                            <td data-title="Area">
                                <?php echo $doctor->area; ?>
                            </td>
                            <td data-title="Doctor Class"><?php echo isset($BasicProfile->class) ? $BasicProfile->class : "-"; ?></td>
                            <td data-title="Priority Product 1">
                                <select  class="form-control " name="product1[]" required >
                                    <?php echo ProductList($Product->product1_id); ?>
                                </select>
                            </td>
                            <td data-title="Priority Product 2">
                                <select  class="form-control " name="product2[]" required >
                                    <?php echo ProductList($Product->product2_id); ?>
                                </select>
                            </td>
                            <td data-title="Priority Product 3">
                                <select  class="form-control " name="product3[]" required>
                                    <?php echo ProductList($Product->product3_id); ?>
                                </select>
                            </td>
                            <td data-title="Total Business">
                                <?php echo isset($lastMonthBusiness->total) ? $lastMonthBusiness->total : "-"; ?>
                            </td>

                        </tr>
                        <tr>
                            <td colspan="8">
                                <input type="submit" name="submit" value="Save" class="btn btn-primary">
                            </td>
                        </tr>
                    </table>
                </form>
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
