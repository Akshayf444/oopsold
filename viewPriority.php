<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");

$empid = $_SESSION['employee'];

$doctors = Doctor::find_all($empid);

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

if (isset($_POST['submit'])) {
    $newPriorityProduct = new PriorityProduct();
    for ($i = 0; $i < count($_POST['docid']); $i++) {
        $newPriorityProduct->id = $_POST['prt_id'][$i];
        $newPriorityProduct->docid = $_POST['docid'][$i];
        $newPriorityProduct->product1_id = $_POST['product1'][$i];
        $newPriorityProduct->product2_id = $_POST['product2'][$i];
        $newPriorityProduct->product3_id = $_POST['product3'][$i];
        $newPriorityProduct->update();
    }

    flashMessage("Record Updated Successfully.", 'success');
    redirect_to('viewPriority.php');
}

require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">View Priority Product</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<form action="" action ="post" >
    <div class="row">
        <div class="col-lg-4">

        </div>     
    </div>
    <div class="row">
        <div class="col-lg-4">

        </div>     
    </div>
</form>
<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="" id="no-more-tables">
            <form action="" method="post" >
                <table class="table table-bordered table-hover ">
                    <thead>
                    <tr>
                        <th>Doctor Name</th><th>MSL Code</th><th>Area</th><th>Doctor Class</th><th>Priority Product 1</th><th>Priority Product 2</th>
                        <th>Priority Product 3</th>
                        <th>Total Business</th>
                    </tr>
                    </thead>
                    <?php
                    foreach ($doctors as $doctor) {

                        $Product = PriorityProduct::find_by_docid($doctor->docid);
                        $BasicProfile = BasicProfile::find_by_docid($doctor->docid);
                        $lastMonthBusiness = BusiProfile::find_by_docid($doctor->docid);
                        $lastMonthBusiness = array_shift($lastMonthBusiness);
                        if (isset($Product->id)) {
                            ?>
                            <tr>
                                <td data-title="Doctor Name">
                                    <input type="hidden" name="prt_id[]" value="<?php echo $Product->id; ?>">
                                    <input type="hidden" name="docid[]" value="<?php echo $doctor->docid; ?>">
                                    <?php echo $doctor->name; ?>
                                </td>
                                <td data-title="MSL Code"><?php echo isset($BasicProfile->msl_code) ? $BasicProfile->msl_code : "-"; ?></td>
                                <td data-title="Area">
                                    <?php echo $doctor->area; ?>
                                </td>
                                <td data-title="Class"><?php echo isset($BasicProfile->class) ? $BasicProfile->class : "-"; ?></td>
                                <td data-title="Priority Product 1">

                                    <?php
                                    $ProductName = Product::find_by_id($Product->product1_id);
                                    echo $ProductName->name
                                    ?>

                                </td>
                                <td data-title="Priority Product 2">
                                    <?php
                                    $ProductName = Product::find_by_id($Product->product2_id);
                                    echo $ProductName->name
                                    ?>

                                </td>
                                <td data-title="Priority Product 3">
                                    <?php
                                    $ProductName = Product::find_by_id($Product->product3_id);
                                    echo $ProductName->name
                                    ?>

                                </td>
                                <td data-title="Total Business">
                                    <?php echo isset($lastMonthBusiness->total) ? $lastMonthBusiness->total : "-"; ?>
                                </td>
                                <td>
                                    <input type="button" id="<?php echo $Product->id ?>" class="btn btn-danger btn-xs" onclick="editPriority(this.id)" value="Edit">
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
<!--                    <tr>
<td colspan="8">
<input type="submit" name="submit" value="Save" class="btn btn-primary">
</td>
</tr>-->
                </table>
            </form>
        </div>
    </div>
</div>
<div id="modalpopup"></div>
<script>
    function editPriority(id) {
        var callid = id;
        $.ajax({
            //Send request
            type: 'POST',
            data: {id: callid},
            url: 'edit_priority.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    }
</script>
<?php
require_once("layouts/TMfooter.php");
