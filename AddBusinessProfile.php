<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$empid = $_SESSION['employee'];
$empname = Employee::find_by_empid($empid);
$pageTitle = "Add Business Profile";
$empName = Employee::find_by_empid($_SESSION['employee']);

$doctors = Doctor::find_all($empid);

$month = date('m', strtotime(date('Y-m') . '-01 -1 months'));
$Brand_business = BrandBusiness::find_by_date($month, $empid);

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

$errors = array();
$errors2 = array();
$idlist = array();
$BusinessIdList = BusiProfile::collect_id($empid);
if (!empty($BusinessIdList)) {
    foreach ($BusinessIdList as $value) {
        array_push($idlist, $value->id);
    }
}

if (isset($_POST['submit'])) {
    for ($i = 0; $i < count($_POST['docid']); $i++) {
        $newBuisnessProfile = new BusiProfile();
        $newBuisnessProfile->docid = $_POST["docid"][$i];
        $newBuisnessProfile->empid = $empid;
        $newBuisnessProfile->month = strftime("%Y-%m-%d ", time());
        $newBuisnessProfile->name = trim($_POST['name'][$i]);
        $newBuisnessProfile->created = strftime("%Y-%m-%d ", time());

        $m = 1;
        $brandCount = 0;
        for ($n = 0; $n < count($_POST[$newBuisnessProfile->docid]); $n++) {
            if ($_POST[$newBuisnessProfile->docid][$n] == '0') {
                $brandCount += 1;
            }
        }

        for ($n = 0; $n < count($_POST[$newBuisnessProfile->docid]); $n++) {
            $id = 0;
            if (isset($_POST[$newBuisnessProfile->docid . '1'][$n])) {
                $id = $_POST[$newBuisnessProfile->docid . '1'][$n];
            };
            $total = $_POST[$newBuisnessProfile->docid . '11'][$n];

            $newBuisnessProfile->brand1 = $_POST[$newBuisnessProfile->docid][$n];
            $newBuisnessProfile->brand_id = $_POST['brandlist' . $m][$i];
            //$businessprofile = BusiProfile::entryExist($id);
            if (in_array($id, $idlist)) {
                $newBuisnessProfile->total = $total;
                $newBuisnessProfile->id = $id;
                $newBuisnessProfile->update();
            } else {
                $newBuisnessProfile->total = $total;
                $newBuisnessProfile->id = 0;
                if ($brandCount < 8) {
                    $newBuisnessProfile->create();
                }
            }

            $m ++;
        }
    }

    redirect_to("AddBusinessProfile.php");
}
?>
<?php require_once("layouts/TMheader.php"); ?>
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
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="table-responsive" id="no-more-tables">
            <form action="" method="post" >
                <table class="table table-bordered table-hover " id="items">
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>Area</th>
                            <?php for ($i = 1; $i < 9; $i++) { ?>
                                <th style="width: 10%">
                                    No. of units
                                </th>
                            <?php } ?>
                            <th style="width: 13%">Total Business</th>
                        </tr>
                    </thead>
                    <?php
                    foreach ($doctors as $doctor) {
                        $BusinessProfile = BusiProfile::find_by_docid($doctor->docid);
                        $total1 = BusiProfile::docwise_business($doctor->docid, $_SESSION['_CURRENT_MONTH']);
                        ?>
                        <tr class="targetfields">
                            <td data-title="Doctor Name"><?php echo $doctor->name; ?></td>
                            <td data-title="Area"><?php
                                echo $doctor->area;
                                $docid = $doctor->docid;
                                ?>
                            </td>
                            <?php
                            $i = 1;
                            if (!empty($BusinessProfile)) {

                                foreach ($BusinessProfile as $buisness) {
                                    ?>
                                    <td data-title="Units">

                                        <input type="hidden" name="<?php echo $docid . "1"; ?>[]" value="<?php echo $buisness->id; ?>">
                                        <select class="form-control brandlist<?php echo $i; ?> commonrate" name="brandlist<?php echo $i; ?>[]" style="padding: 0px;height: 20px;margin-bottom: 0.2em; font-size: 12px">
                                            <?php
                                            if (isset($buisness->brand_id)) {
                                                echo ProductList($buisness->brand_id);
                                            } else {
                                                echo ProductList();
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" name="<?php echo $docid . "11"; ?>[]" value="0" class="ptstotal<?php echo $i; ?>">
                                        <input type="text" class="form-control common brand<?php echo $i; ?>" name="<?php echo $docid; ?>[]" maxlength="50" value="<?php
                                if (isset($buisness->brand1)) {
                                    echo $buisness->brand1;
                                }
                                            ?>" />

                                    </td>
                                    <?php
                                    if ($i == 8) {
                                        break;
                                    }
                                    $i++;
                                }
                            } else {

                                for ($k = 1; $k < 9; $k++) {
                                    ?>
                                    <td data-title="Units">
                                        <select class="form-control brandlist<?php echo $k; ?> commonrate" name="brandlist<?php echo $k; ?>[]" style="padding: 0px;height: 20px;margin-bottom: 0.2em; font-size: 12px">
                                            <?php
                                            echo ProductList();
                                            ?>
                                        </select>
                                        <input type="hidden" name="<?php echo $docid . "11"; ?>[]" value="0" class="ptstotal<?php echo $k; ?>">
                                        <input type="text" class="form-control common brand<?php echo $k; ?>" name="<?php echo $docid; ?>[]" maxlength="50" value="0" />
                                    </td>
                                    <?php
                                }
                            }
                            ?>
                            <td data-title="Total">
                                <input type="hidden" name="docid[]" value="<?php echo $doctor->docid ?>">
                                <input type="hidden" name="name[]" value="<?php echo $doctor->name ?>">
                                <a href="addMoreBusiness.php?id=<?php echo $docid ?>" class="btn btn-xs btn-demo">Add More</a>
                                <input type="text" name="total[]" readonly class="form-control total" maxlength="50" value="<?php
                        echo $total1;
                            ?>" />
                                <input type="hidden" class="hiddenTotal" value="<?php echo $total1; ?>">
                            </td>

                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="11">
                            <input type="submit" name="submit" class="btn btn-primary" value="Add Details" style="display:<?php
                    if ($empname->lock_buisness == 1) {
                        echo "none";
                    }
                    ?>" />
                        </td>
                    </tr>

                </table>
            </form>
        </div>
    </div>
</div>
<script>
    jQuery(function () {
        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;
        //function for calculating total and GraandTotal 
        jQuery("#items ").delegate('.common ', 'keyup', function () {
            var total = 0;
            jQuery("#items .targetfields").each(function () {
                //get the  values of quantity and rate 
                var rate1 = jQuery(this).find(".brandlist1").find(':selected').data('rate');
                var val1 = jQuery(this).find(".brand1").val() * rate1;
                var val2 = jQuery(this).find(".brand2").val() * jQuery(this).find(".brandlist2").find(':selected').data('rate');
                var val3 = jQuery(this).find(".brand3").val() * jQuery(this).find(".brandlist3").find(':selected').data('rate');
                var val4 = jQuery(this).find(".brand4").val() * jQuery(this).find(".brandlist4").find(':selected').data('rate');
                var val5 = jQuery(this).find(".brand5").val() * jQuery(this).find(".brandlist5").find(':selected').data('rate');
                var val6 = jQuery(this).find(".brand6").val() * jQuery(this).find(".brandlist6").find(':selected').data('rate');
                var val7 = jQuery(this).find(".brand7").val() * jQuery(this).find(".brandlist7").find(':selected').data('rate');
                var val8 = jQuery(this).find(".brand8").val() * jQuery(this).find(".brandlist8").find(':selected').data('rate');

                //assign individual total
                jQuery(this).find(".ptstotal1").val(val1);
                jQuery(this).find(".ptstotal2").val(val2);
                jQuery(this).find(".ptstotal3").val(val3);
                jQuery(this).find(".ptstotal4").val(val4);
                jQuery(this).find(".ptstotal5").val(val5);
                jQuery(this).find(".ptstotal6").val(val6);
                jQuery(this).find(".ptstotal7").val(val7);
                jQuery(this).find(".ptstotal8").val(val8);

                var subtotal = val1 + val2 + val3 + val4 + val5 + val6 + val7 + val8;

                jQuery(this).find(".total").val(subtotal.toFixed(2));

            });

        });

        jQuery("#items ").delegate('.commonrate ', 'change', function () {

            var total = 0;
            jQuery("#items .targetfields").each(function () {
                //get the  values of quantity and rate 
                var rate1 = jQuery(this).find(".brandlist1").find(':selected').data('rate');
                var val1 = jQuery(this).find(".brand1").val() * rate1;
                var val2 = jQuery(this).find(".brand2").val() * jQuery(this).find(".brandlist2").find(':selected').data('rate');
                var val3 = jQuery(this).find(".brand3").val() * jQuery(this).find(".brandlist3").find(':selected').data('rate');
                var val4 = jQuery(this).find(".brand4").val() * jQuery(this).find(".brandlist4").find(':selected').data('rate');
                var val5 = jQuery(this).find(".brand5").val() * jQuery(this).find(".brandlist5").find(':selected').data('rate');
                var val6 = jQuery(this).find(".brand6").val() * jQuery(this).find(".brandlist6").find(':selected').data('rate');
                var val7 = jQuery(this).find(".brand7").val() * jQuery(this).find(".brandlist7").find(':selected').data('rate');
                var val8 = jQuery(this).find(".brand8").val() * jQuery(this).find(".brandlist8").find(':selected').data('rate');

                //calculate subtotal
                jQuery(this).find(".ptstotal1").val(val1);
                jQuery(this).find(".ptstotal2").val(val2);
                jQuery(this).find(".ptstotal3").val(val3);
                jQuery(this).find(".ptstotal4").val(val4);
                jQuery(this).find(".ptstotal5").val(val5);
                jQuery(this).find(".ptstotal6").val(val6);
                jQuery(this).find(".ptstotal7").val(val7);
                jQuery(this).find(".ptstotal8").val(val8);

                var subtotal = val1 + val2 + val3 + val4 + val5 + val6 + val7 + val8;

                jQuery(this).find(".total").val(subtotal.toFixed(2));

            });
        });
    });
</script>
<?php require_once("layouts/TMfooter.php"); ?>