<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];

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

if (isset($_GET['id'])) {
    $docid = $_GET['id'];
    $Doctor = Doctor::find_by_docid($docid);
    $BusinessProfile = BusiProfile::find_by_docid($docid);
    $BusinessProfile = array_shift($BusinessProfile);
    $RemainingRecords = BusiProfile::find_morethan_8_brands($docid);
}

if (isset($_POST['addmore'])) {

    $newBuisnessProfile = new BusiProfile();
    for ($i = 0; $i < count($_POST['brand1']); $i++) {
        $newBuisnessProfile->docid = $_GET['id'];
        $newBuisnessProfile->empid = $empid;
        $newBuisnessProfile->month = strftime("%Y-%m-%d ", time());

        $newBuisnessProfile->created = strftime("%Y-%m-%d ", time());

        $newBuisnessProfile->brand1 = $_POST['brand1'][$i];
        $newBuisnessProfile->brand_id = $_POST['brandlist'][$i];
        $newBuisnessProfile->total = $_POST['total'][$i];
        if (isset($_POST['ids'][$i])) {
            $newBuisnessProfile->id = $_POST['ids'][$i];
            $newBuisnessProfile->update();
        } else {
            $newBuisnessProfile->id = 0;
            $newBuisnessProfile->create();
        }
    }
    redirect_to("AddBusinessProfile.php");
}
$empid = $_SESSION['employee'];
require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Business For <?php
            $currentMonth = date('F');
            echo date('F', strtotime(date('Y-m') . '-01 -1 months'));
            ?>
        </h1>

    </div>
</div>
<div class="row">
    <div class="col-lg-8 col-sm-8 col-md-8 col-xs-12">
        <div class="pull-left" >
            <input type="text" class="form-control total" name="total" >
        </div>
        <form action="" method="post">
            <div class="">
                <div class="row ">
                    <div class="col-lg-12">

                        <div class="pull-right ">
                            <input type="button" class="btn btn-danger" value="Add More">
                        </div>
                    </div>
                </div>
                <table class="table table-bordered row-margin-top"  id="items">
                    <tbody class="addBrand">
                        <?php
                        if ($RemainingRecords != FALSE) {
                            foreach ($RemainingRecords as $record) {
                                ?>
                                <tr>
                                    <td>
                                        <select class="form-control brandlist commonrate" name="brandlist[]" >
                                            <?php
                                            echo ProductList($record->brand_id);
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="ids[]" value="<?php echo $record->id; ?>">
                                        <input type="hidden" name="total[]" class="ptsrate" value="0">
                                        <input type="text" class="form-control common brand" name="brand1[]" maxlength="50" value="<?php echo $record->brand1; ?>" />
                                    </td>
                                </tr>
                                <?php
                            }
                        }else {
                        ?>
                        <tr>
                            <td>
                                <select class="form-control brandlist commonrate" name="brandlist[]" >
                                    <?php
                                    echo ProductList();
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="total[]" class="ptsrate" value="0">
                                <input type="text" class="form-control common brand" name="brand1[]" maxlength="50" value="0" />
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>

                    <tr>
                        <td colspan="2">
                            <input type="submit" class="btn btn-primary" value="Save" name="addmore">
                        </td>
                    </tr>
                </table>

            </div>
        </form>
    </div>
</div>
<script>
    jQuery(function () {
        jQuery("#items ").delegate('.common ', 'keyup', function () {
            var subtotal = 0;
            jQuery(".brandlist").each(function () {
                var val = jQuery(this).find(':selected').data('rate') * jQuery(this).closest('tr').find(".brand").val();
                jQuery(this).closest('tr').find(".ptsrate").val(val)
                subtotal = parseFloat(subtotal) + parseFloat(val);
            });
            jQuery(".total").val(subtotal.toFixed(2));

        });

        $(".btn-danger").click(function () {
            var addBusinessRow = 0;
            $.ajax({
                //Send request
                type: 'POST',
                data: {addBusinessRow: addBusinessRow},
                url: 'getNewRow.php',
                success: function (data) {
                    $(".addBrand").append(data);
                }
            });
        });
    });
</script>
<?php require_once("layouts/TMfooter.php"); ?>