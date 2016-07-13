<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

if (isset($_POST['mobile'])) {
    $Employee = Employee::updateMobile($_SESSION['employee'], $_POST['mobile']);
} elseif (isset($_POST['dob'])) {
    $Employee = Employee::updatedob($_SESSION['employee'], $_POST['dob']);
} elseif (isset($_POST['doa'])) {
    $Employee = Employee::updatedoa($_SESSION['employee'], $_POST['doa']);
}
$empName = Employee::find_by_empid($_SESSION['employee']);
?>
<script src="js/jquery-1.11.0.js"></script>
<script src="js/jquery-ui.js" type="text/javascript"></script>
<script>
    $(function () {
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1901:2014"
        });

    });
</script>
<script>
    $(function () {
        $("#datepicker1").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1901:2014"
        });

    });
</script>
<table cellspacing="0" class="table table-bordered">
    <tr>
        <th>Email</th>
        <td><?php echo $empName->emailid; ?></td>
    </tr>
    <tr>
        <th>Mobile</th>
        <td>
            <a href="#" class="mobileshow" onclick="editMobile()"><?php echo $empName->mobile ?></a>
            <div class="row mobilehide col-lg-6" style="display: none">
                <input type="text" name="mobile" value="<?php echo $empName->mobile ?>" class="form-control mobile ">
                <input type="button" name="submit" value="Save" class="btn btn-info btn-xs " id="submit">
            </div>

        </td>
    </tr>
    <tr>
        <th>Reporting BM</th>
        <td>
            <?php
            $BM = BM::find_by_bmid($empName->bm_empid);
            if (isset($BM->name)) {
                echo $BM->name;
            }
            ?>                            
        </td>
    </tr>
    <tr>
        <th>Reporting SM</th>
        <td><?php
            $SM = SM::find_by_smid($BM->sm_empid);
            if (isset($SM->name)) {
                echo $SM->name;
            }
            ?></td>
    </tr>
    <tr>
        <th>HQ</th>
        <td><?php echo $empName->HQ; ?></td>
    </tr>
    <tr>
        <th>Date Of Birth</th>
        <td>
            <span class="dobshow" ><?php echo date('d-m-y', strtotime($empName->DOB)) ?></span>&nbsp;&nbsp;<a href="#" onclick= "editDob()">Edit</a>
            <div class="row dobhide col-lg-6 " style="display: none">
                <input type="text" name="dob" value="<?php echo $empName->DOB ?>" class="form-control dob " id="datepicker" >
                <input type="button" name="submit" value="Save" class="btn btn-info btn-xs " id="dob" style="margin-top: 2px">
            </div>

        </td>
    </tr>
    <tr>
        <th>Date Of Anniversary</th>
        <td>
            <span class="doashow" ><?php echo date('d-m-y', strtotime($empName->doa)) ?></span>&nbsp;&nbsp;<a href="#" onclick= "editDoa()">Edit</a>
            <div class="row doahide col-lg-6 " style="display: none">
                <input type="text" name="doa" value="<?php echo $empName->doa ?>" class="form-control doa " id="datepicker1" >
                <input type="button" name="submit" value="Save" class="btn btn-info btn-xs " id="doa" style="margin-top: 2px">
            </div>
        </td>
    </tr>
</table>