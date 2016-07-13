<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];

function ProductList($id = "") {
    $Products = Product::find_all();
    $ProductList = '';
    foreach ($Products as $Product) {
        if ($id == $Product->id) {
            $ProductList .= $Product->name;
        }
    }
    return $ProductList;
}

if (isset($_POST['month']) && $_POST['month'] != '0') {
    $month = $_POST['month'];
    $BusinessProfile = BrandBusiness::find_by_month($month, $empid);
} else {
    $month = $_SESSION['_CURRENT_MONTH'];
    $BusinessProfile = BrandBusiness::find_by_month($month, $empid);
}
?>
<?php require_once("layouts/TMheader.php"); ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Month wise Business</h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 center-block"> 
        <select class="form-control" id="month" onchange="isDate()" name="month">
            <option value="">SELECT MONTH</option>
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $month = $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                echo '<option value="' . $m . '">' . $month . '</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php
foreach ($BusinessProfile as $doctor):
    $Doctor_details = Doctor::find_by_docid($doctor->docid);
    ?>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover " id="items">
                    <tr>
                        <th>Doctor Name</th>
                        <th>Area</th>
                        <?php for ($i = 1; $i < 9; $i++) { ?>
                            <th style="width: 10%">
                                <?php
                                if (!empty($doctor)) {
                                    $data = explode(",", $doctor->{'brand' . $i});
                                    echo ProductList($data[0]);
                                }
                                ?>
                            </th>
                        <?php } ?>
                        <th style="width: 13%">Total Business</th>
                    </tr>
                    <tr class="targetfields">
                        <td><?php echo $Doctor_details->name; ?></td>
                        <td><?php
                            echo $Doctor_details->area;
                            $docid = $doctor->docid;
                            ?>
                        </td>
                        <?php for ($i = 1; $i < 9; $i++) { ?>
                            <td >
                                <?php
                                if (!empty($doctor)) {
                                    $data = explode(",", $doctor->{'brand' . $i});
                                    echo $data[1];
                                }
                                ?>
                            </td>
                        <?php } ?>
                        <td><?php
                            if (!empty($doctor)) {
                                echo $doctor->total;
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php require_once("layouts/TMfooter.php"); ?>