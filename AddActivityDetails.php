<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
require_once(dirname(__FILE__) . "/includes/class.activity_master.php");
$Activities = ActivityMaster::find_all();
$output = '';
$Products = Product::find_all();
$Doctors = Doctor::find_all($_SESSION['employee']);

$ProductList = '<option value ="0" data-rate="0" > Select Brand </option>';
foreach ($Products as $Product) {
    $ProductList .='<option value ="' . $Product->id . '" data-rate="' . $Product->pts . '" >' . $Product->name . '</option>';
}

$output .= '<option>Select Activity</option>';
foreach ($Activities as $Activity) {
    $output .='<option value ="' . $Activity->id . '" >' . $Activity->activity . '</option>';
}

$pageTitle = "Add Activity Details";
$errors2 = array();
if (isset($_POST['submit'])) {

    $newActivity = new Activity();
    $newActivity->act_id = 0;
    if ($_POST['activity_type'] == '12') {
        $newActivity->activity_type = $_POST['activity_type1'];
    } else {
        $newActivity->activity_type = $_POST['activity_type'];
    }

    if (!empty($_POST['activity_date'])) {
        $newActivity->activity_date = $_POST['activity_date'];
    } else {
        array_push($errors2, "Please Enter Activity Date.");
    }

    if (!empty($_POST['doctor_name'])) {
        $newActivity->doc_id = $_POST['doctor_name'];
    } else {
        array_push($errors2, "Please Enter Doctor Name");
    }

    if (!empty($_POST['expances'])) {
        $newActivity->expances = $_POST['expances'];
    } else {
        array_push($errors2, "Please Enter Expances Details");
    }

    $newActivity->brand1 = $_POST['brand1'];
    $newActivity->brand2 = $_POST['brand2'];
    $newActivity->brand3 = $_POST['brand3'];
    $newActivity->brand4 = $_POST['brand4'];
    $newActivity->brand5 = $_POST['brand5'];
    $newActivity->brand6 = $_POST['brand6'];
    $newActivity->brand7 = $_POST['brand7'];
    $newActivity->brand8 = $_POST['brand8'];
    $newActivity->total = $_POST['total'];
    $newActivity->highlight = $_POST['highlight'];
    $newActivity->empid = $_SESSION['employee'];

    $filenames = array();
    if (isset($_FILES['files'])) {
        $errors = array();
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {

            if ($_FILES['files']['name'][$key] != '') {
                $file_name = date(time()) . $_FILES['files']['name'][$key];
                array_push($filenames, $file_name);
            } else {
                array_push($errors, 'FileName Cannot Be Empty.');
            }

            $file_size = $_FILES['files']['size'][$key];
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $file_type = $_FILES['files']['type'][$key];
            if ($file_size > 2097152) {
                $errors[] = 'File size must be less than 2 MB';
            }

            $desired_dir = "activities";
            if (empty($errors) == true) {
                if (is_dir($desired_dir) == false) {
                    mkdir("$desired_dir", 0700);  // Create directory if it does not exist
                }
                if (is_dir("$desired_dir/" . $file_name) == false) {
                    move_uploaded_file($file_tmp, "$desired_dir/" . $file_name);
                } else {         // rename the file if another one exist
                    $new_dir = "$desired_dir/" . $file_name . time();
                    rename($file_tmp, $new_dir);
                }
            }
        }
    }
    if (!empty($filenames)) {
        $newActivity->filename = join(",", $filenames);
    }


    /*     * ************************ Add brand value ****************************** */


    if (empty($errors2)) {
        $business = Activity::find_by_actid($newActivity->act_id);
        if (!empty($business)) {
            $newActivity->update($newActivity->act_id);
        } else {
            $newActivity->act_id = $newActivity->create();
        }
    }



    $BrandActivity = new BrandActivity();
    for ($i = 1; $i <= 8; $i++) {
        $BrandActivity->{'brand' . $i} = $_POST['brandlist' . $i];
    }
    $BrandActivity->act_id = $newActivity->act_id;
    $BrandActivity->create();

    if (empty($errors2)) {
        flashMessage("Record Added Successfully.", 'Success');
        redirect_to("AddActivityDetails.php");
    }
}
?>
<?php require_once("layouts/TMheader.php"); ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Activity Details</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        };
        ?>
        <ul>
            <?php foreach (array_unique($errors2) as $val) { ?>
                <li style="color:red"><?php echo $val; ?></li>
            <?php } ?>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <form action="AddActivityDetails.php" method="post"  enctype="multipart/form-data">  
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-12 col-md-12 col-xs-12">
                    <label>Activity Type</label>
                    <select name="activity_type" class="form-control " style="" required id="activity">
                        <?php echo $output; ?>

                    </select>
                    <div class="row" id="activity_type1">
                        <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                            <label>Pls Specify Here</label>
                            <input type="text" name="activity_type1" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                    <label>Activity Date</label>
                    <input type="text" name="activity_date" value="<?php
                    if (isset($_POST['activity_date'])) {
                        echo $_POST['activity_date'];
                    }
                    ?>" id="datepicker" autocomplete="off"  class="form-control" style="" />
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                    <label>Doctor Name</label>
                    <select name="doctor_name" class="form-control" style="">
                        <option> Select Doctor</option>
                        <?php foreach ($Doctors as $doctor) { ?>
                            <option value="<?php echo $doctor->docid ?>" ><?php echo $doctor->name ?></option>                   
                        <?php } ?>

                    </select>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-6 col-xs-12">
                    <label>Expenses</label>
                    <input type="text" name="expances" value="<?php
                    if (isset($_POST['expances'])) {
                        echo $_POST['expances'];
                    }
                    ?>"  class="form-control" style="" placeholder="Enter Value in Rs."/>
                </div>
            </div>
            <div class="row" style="z-index:1">
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <label>Rx GrandTotal</label>
                </div>
            </div>
            <div class="row" >
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover " id="items" >
                        <tr>
                            <?php for ($i = 1; $i <= 8; $i++) { ?>
                                <th>
                                    <select style="
                                            padding: 0px;
                                            height: 20px;
                                            margin-bottom: 0.2em;
                                            font-size: 12px;
                                            "  name="brandlist<?php echo $i; ?>" class="form-control brand<?php echo $i; ?>">
                                                <?php echo $ProductList; ?>
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
                                    if (isset($_POST['brand' . $i])) {
                                        echo $_POST['brand' . $i];
                                    }
                                    ?>" class="form-control common" id="brand<?php echo $i . "1"; ?>"/>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="text" name="total" value="" class="form-control subtotal" readonly/>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-8 col-sm-8 col-md-8 col-xs-12">
                    <label>Highlights Of Activity</label>
                    <textarea name="highlight" cols="30" rows="4" class="form-control" style="width:100%"><?php
                        if (isset($_POST['highlight'])) {
                            echo $_POST['highlight'];
                        }
                        ?></textarea>
                </div>
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <label>Attachments</label>
                    <input type="file" name="files[]" multiple>
                </div>
            </div>
            <hr/>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <input type="submit" name="submit" value="Save" class="btn btn-primary "/>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $("#activity_type1").css("display", "none");
        // Add onclick handler to checkbox w/id checkme

        $('#activity').change(function () {
            if ($(this).val() == '12') {
                $('#activity_type1').css({'display': 'block'});
            } else {
                $('#activity_type1').css({'display': 'none'});
            }
        });
    });
</script>
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
    $(function () {
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
<?php require_once("layouts/TMfooter.php"); ?>