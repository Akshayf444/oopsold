<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
$Products = Product::find_all();
$doctors = Doctor::find_all($empid);
$newDoctorEntry = TRUE;

$ProductList = '<option value="0" >Select Brand</option>';
foreach ($Products as $Product) {
    $ProductList .='<option value ="' . $Product->id . '"  >' . $Product->name . '</option>';
}

if (isset($_POST['submit'])) {
    $newPriorityProduct = new PriorityProduct();
    for ($i = 0; $i < count($_POST['docid']); $i++) {
        $newPriorityProduct->docid = $_POST['docid'][$i];
        $newPriorityProduct->product1_id = $_POST['product1'][$i];
        $newPriorityProduct->product2_id = $_POST['product2'][$i];
        $newPriorityProduct->product3_id = $_POST['product3'][$i];
        if ($newPriorityProduct->product1_id != 0 && $newPriorityProduct->product2_id != 0 && $newPriorityProduct->product3_id != 0) {
            $newPriorityProduct->create();
        }
    }

    redirect_to('viewPriority.php');
}
require_once("layouts/TMheader.php");
?>
<script src="js/validate.js" type="text/javascript"></script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Priority Product</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 clone-group">
        <div class=" clone" id="no-more-tables">
            <form action="" method="post" name="test" >
                <table class="table table-bordered table-hover ">
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>MSL Code</th>
                            <th>Area</th>
                            <th>Doctor Class</th>
                            <th>Priority Product 1</th><th>Priority Product 2</th><th>Priority Product 3</th>
                            <th>Total Business</th>
                        </tr>
                    </thead>
                    <?php
                    foreach ($doctors as $doctor) {
                        $PriorityProductExist = PriorityProduct::find_by_docid($doctor->docid);
                        $BasicProfile = BasicProfile::find_by_docid($doctor->docid);
                        $lastMonthBusiness = BusiProfile::find_by_docid($doctor->docid);
                        $lastMonthBusiness = array_shift($lastMonthBusiness);
                        if (empty($PriorityProductExist)) {
                            ?>
                            <tr>
                                <td data-title="Doctor Name">
                                    <input type="hidden" name="docid[]" value="<?php echo $doctor->docid; ?>">
                                    <?php echo $doctor->name; ?>
                                </td>
                                <td data-title="MSL Code"><?php echo isset($BasicProfile->msl_code) ? $BasicProfile->msl_code : "-"; ?></td>
                                <td data-title="Area">
                                    <?php echo $doctor->area; ?>
                                </td>
                                <td data-title="Doctor Class"><?php echo isset($BasicProfile->class) ? $BasicProfile->class : "-"; ?></td>

                                <td data-title="Priority Product 1">
                                    <select  class="form-control " name="product1[]" required="required" >
                                        <?php echo $ProductList; ?>
                                    </select>
                                </td>
                                <td data-title="Priority Product 2">
                                    <select  class="form-control " name="product2[]" required="required" >
                                        <?php echo $ProductList; ?>
                                    </select>
                                </td>
                                <td data-title="Priority Product 3">
                                    <select  class="form-control " name="product3[]" required="required" >
                                        <?php echo $ProductList; ?>
                                    </select>
                                </td>
                                <td data-title="Total Business">
                                    <?php echo isset($lastMonthBusiness->total) ? $lastMonthBusiness->total : "-"; ?>
                                </td>
                            </tr>
                            <?php
                        } else {
                            $newDoctorEntry = FALSE;
                        }
                    }
                    ?>
                    <?php
                    if ($newDoctorEntry == FALSE) {
                        //echo '<tr><td colspan = "5"> Data Not Found</td></tr>';
                    }
                    ?>
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
<script>
    $(function () {
        var cloneItem = $(".clone");
        var cloneWrap = $(".clone-group");

        $(".addButton").on("click", function () {
            cloneItem.clone().appendTo(cloneWrap).find(".addButton")
                    .removeClass("addButton").addClass("remButton").text("-");



            $("input").each(function () {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Specify the reference name"
                    }
                });
            });
        });

        $("body").on("click", ".remButton", function () {
            $(this).closest(".clone").remove();
        });
        $("form").validate({
            rules: {
                "product1[]": {
                    required: true
                },
                "product2[]": {
                    required: true
                },
                "product3[]": {
                    required: true
                }
            },
            messages: {
                "product1[]": {
                    required: "This field is required"
                },
                "product2[]": {
                    required: "This field is required"
                },
                "product3[]": {
                    required: "This field is required"
                }
            },
            errorClass: "invalid"
        });
    });
</script>
<?php
require_once("layouts/TMfooter.php");
