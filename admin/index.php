<?php

session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$errors = array();
$fields = array();
$newEmployee = new Employee();
if (isset($_POST['submit']) && ($_POST['emptype'] == 'TM')) {
    $oldempid = $_POST['oldempid'];

    if (!empty($_POST['empid'])) {

        $newIDCheck = Employee::cipla_empid($oldempid);
        if (!empty($newIDCheck)) {
            $newEmployee->cipla_empid = trim($_POST['empid']);
            $newEmployee->empid = $newIDCheck->empid;
        } else {
            array_push($errors, "Old Employee Dosnt Exist");
        }
    } else {
        array_push($errors, "Empid cant be blank");
    }

    if (!empty($_POST['name'])) {
        $newEmployee->name = trim($_POST['name']);
        array_push($fields, $newEmployee->name);
    } else {
        array_push($errors, "Name cant be blank");
    }

    $newEmployee->emailid = trim($_POST['emailid']);
    $newEmployee->password = trim($_POST['password']);
    $newEmployee->mobile = trim($_POST['mobile']);

    if (empty($errors)) {
        $result = $newEmployee->SelectiveUpdate();
        if ($result) {
            redirect_to("index.php");
        } else {
            array_push($errors, "Failed");
        }
    }
} else {

    //update BM details
    if (isset($_POST['submit']) && ($_POST['emptype'] == 'BM')) {
        $newBM = new BM();
        $oldempid = $_POST['oldempid'];
        if (!empty($_POST['empid'])) {
            $newIDCheck = BM::find_by_bmid(trim($_POST['empid']));
            if (!empty($newIDCheck)) {
                array_push($errors, "Your new BM-id is already assigned to someone else.");
            } else {
                $newBM->bm_empid = trim($_POST['empid']);
                array_push($fields, $newBM->bm_empid);
            }
        } else {
            array_push($errors, "Empid cant be blank");
        }

        if (!empty($_POST['name'])) {
            $newBM->name = trim($_POST['name']);
            array_push($fields, $newBM->name);
        } else {
            array_push($errors, "Name cant be blank");
        }

        $newBM->emailid = trim($_POST['emailid']);
        $newBM->password = trim($_POST['password']);
        $newBM->mobile = trim($_POST['mobile']);

        array_push($fields, $newBM->password);
        array_push($fields, $newBM->emailid);
        array_push($fields, $newBM->mobile);



        if (empty($errors)) {
            $result = $newBM->SelectiveUpdate($oldempid, $fields);
            if ($result) {
                redirect_to("index.php");
            } else {
                array_push($errors, "Failed");
            }
        }
    }
}

if (isset($_POST['submit']) && ($_POST['emptype'] == 'SM')) {
    $newSM = new SM();
    $oldempid = $_POST['oldempid'];
    if (!empty($_POST['empid'])) {
        $newIDCheck = SM::find_by_smid(trim($_POST['empid']));
        if (!empty($newIDCheck)) {
            array_push($errors, "Your new SM-id is already assigned to someone else.");
        } else {
            $newSM->sm_empid = trim($_POST['empid']);
            array_push($fields, $newSM->bm_empid);
        }
    } else {
        array_push($errors, "Empid cant be blank");
    }

    if (!empty($_POST['name'])) {
        $newSM->name = trim($_POST['name']);
        array_push($fields, $newSM->name);
    } else {
        array_push($errors, "Name cant be blank");
    }

    $newSM->emailid = trim($_POST['emailid']);
    array_push($fields, $newSM->emailid);

    $newSM->password = trim($_POST['password']);
    array_push($fields, $newSM->password);

    $newSM->mobile = trim($_POST['mobile']);
    array_push($fields, $newSM->mobile);


    if (empty($errors)) {
        $result = $newSM->SelectiveUpdate($oldempid, $fields);
        if ($result) {
            redirect_to("index.php");
        } else {
            array_push($errors, "Failed");
        }
    }
}

require_once("adminheader.php");
?>

<script>
    function Search() {
        $("#employee").css("background", " url('../images/loader.gif') no-repeat scroll center center ");
        var search_term = $("#type").val() + " " + $("#oldid").val();
        $.post('getEmployeeDetails.php', {search_term: search_term}, function (data) {
            $("#employee").css("background", "#fff");
            $('#employee').html(data);
        });
    }
</script>
<script>
    function funCalculateTotal(id1) {
        $('#error').empty();
        if ($("#" + id1).val().trim().length == 0) {
            $("#error").append('<li>Please specify New Empid.</li>');
        }
    }
</script>
<script>
    function username(id2) {
        $('#error').empty();
        if ($("#" + id2).val().trim().length == 0) {
            $("#error").append('<li>Please specify your Name.</li>');
        }
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Replace Employee</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<style>
    .form-control{
        display: inline;
        width: 80%;
    }
</style>
<div class="row">
    <div  style="" class="col-lg-6 col-sm-6 col-md-6 col-xs-6">          
        <form action="index.php" method="post" >
            <div class="">
                <table class="table table-bordered table-responsive">
                    <tr>
                        <td>For </td>
                        <td>
                            <select name="emptype" id="type" class="form-control">
                                <option value='TM'>TM</option>
                                <option value='BM'>BM</option>
                                <option value='SM'>SM</option>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td>Old Emp-ID</td>
                        <td>
                            <input type="text" name="oldempid" id="oldid" maxlength="30" value=""  class="form-control"/>
                            <input type="button"  maxlength="30" value="View" onclick="Search()" class="btn btn-danger btn-xs" />
                        </td>

                    </tr>

                    <tr>
                        <td>New Emp-ID</td>
                        <td>
                            <input type="text" name="empid" maxlength="30" value="" id="1" required onchange="funCalculateTotal('1')" class="form-control"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>
                            <input type="text" name="name" maxlength="30" id="2" value="" required onchange="username('2')" class="form-control"/>
                        </td>
                    </tr>

                    <tr>
                        <td>Email-id:</td>
                        <td>
                            <input type="text" name="emailid" maxlength="50" value="" id="3"  class="form-control" />
                        </td>
                    </tr>

                    <tr>
                        <td>Password:</td>
                        <td>
                            <input type="password" name="password" maxlength="30" value="" id="4"  class="form-control"/>
                        </td>

                    </tr>

                    <tr>
                        <td>Mobile:</td>
                        <td>
                            <input type="text" name="mobile" maxlength="10" value=""  id="5" class="form-control"/>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <input type="submit" name="submit" value="Save" class="btn btn-primary"/>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
    <div class=" table-responsive">
        <div  id="employee" style="" class="col-lg-6 col-sm-6 col-md-6 col-xs-6"></div>
    </div>
</div>
<?php

require_once("adminfooter.php");
