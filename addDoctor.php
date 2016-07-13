<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$empid = $_SESSION['employee'];
$AreaList = areaList();

$pageTitle = "Add Doctor";
$empName = Employee::find_by_empid($_SESSION['employee']);

$errors = array();
$newDoctor = new Doctor();
$newDoctor->docid = '';

$empid = $_SESSION['employee'];

if (isset($_POST['submit'])) {

    for ($i = 0; $i < 10; $i++) {
        //$newDoctor->docid=$_POST["docid"][$i];
        $newDoctor->empid = $empid;
        $newDoctor->emailid = trim($_POST['emailid'][$i]);
        if (!empty($_POST['name'][$i])) {
            $newDoctor->name = trim($_POST['name'][$i]);
        } else {
            array_push($errors, "Invalid Email Address");
        }

        $newDoctor->mobile = trim($_POST['mobile'][$i]);
        $newDoctor->area = trim($_POST['area'][$i]);
        $newDoctor->speciality = trim($_POST['speciality'][$i]);
        if (empty($errors)) {
            $newDoctor->create();
        }
    }

    flashMessage("Record Saved Successfully.....", 'success');
    redirect_to("addDoctor.php");
}
?>
<?php require_once("layouts/TMheader.php"); ?>
<!--Script for unabling and disabling textboxes-->
<!-- Showing errors-->

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Doctor Details</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<div class="row">	
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" id="no-more-tables">
        <form action="addDoctor.php" method="post" name="form" >
            <table class="table table-bordered table-hover " id="items">
                <thead>
                    <tr>
                        <th>Name </th>
                        <th>Speciality</th>
                        <th>Email-id</th>
                        <th>Mobile</th>
                        <th>Area</th>

                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < 10; $i++) { ?>
                        <tr>
                            <td data-title="Name"><input type="text" class="form-control name" name="name[]" maxlength="30" /></td>
                            <td data-title="Speciality">
                                <select name="speciality[]" class="form-control">
                                    <option value='Glaucoma'>Glaucoma</option>
                                    <option value='Cornea'>Cornea</option>
                                    <option value='Retina'>Retina</option>
                                    <option value='General'>General</option>
                                    <option value='Other'>Other</option>
                                </select>		
                            </td>
                            <td data-title="Email-id"><input type="text" class="form-control emailid" name="emailid[]" maxlength="50"  /></td>
                            <td data-title="Mobile"><input type="text" class="form-control mobile" name="mobile[]" maxlength="10" /></td>
                            <td data-title="Area">
                                <select class="form-control area" name="area[]" >
                                    <?php echo $AreaList; ?>
                                </select>
                            </td>

                        </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="5">
                            <input type="submit" name="submit" class="btn btn-primary" value="Add Details" id="Save"/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<script language="javascript" type="text/javascript">
    function funCalculateTotal(id1, id2, id3, id4) {

    }

    jQuery(function () {
        $('.emailid').change(function () {
            var reEmail = /^[\w-|+|'|]+(\.[\w-|+|'|]+)*@([a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*?\.[a-zA-Z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/
            if ($(this).val().length == 0) {
                alert('Please Enter Emailid');
            }

            else {
                if (!(reEmail.test($(this).val()))) {
                    alert('Invalid Email Id');
                }
            }
        });

        $('.mobile').change(function () {
            var mobile = /[0-9]{10}/;
            if ($(this).val().length == 0) {
                alert('Please Enter Mobile No');
            }
            else {
                if (!(mobile.test($(this).val()))) {
                    alert('Invalid Mobile No');
                }
            }
        });

        $('.name').change(function () {
            if ($(this).val().length == 0) {
                alert('Please Enter Doctor Name');
            }
        });

        $('.area').change(function () {
            if ($(this).val().length == 0) {
                alert('Please Select Doctor Area');
                $("#Save").hide();
            } else {
                $("#Save").show();
            }
        });

    });
</script>
<?php require_once("layouts/TMfooter.php"); ?>