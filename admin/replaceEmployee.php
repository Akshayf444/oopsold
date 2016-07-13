<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$errors = array();
$fields = array();

if (isset($_POST['submit']) && ($_POST['emptype'] == 'TM')) {

    //Replace TM details
    $newEmployee = new Employee();
    if (!empty($_POST['oldempid'])) {
        $employee = Employee::find_by_empid($_POST['oldempid']);
        if (!empty($employee)) {
            $oldempid = trim($_POST['oldempid']);
        } else {
            array_push($errors, "Employee-id dosn't exist");
        }
    } else {
        array_push($errors, "Old Empid cant be blank");
    }

    if (!empty($_POST['empid'])) {
        $employee = Employee::find_by_empid($_POST['oldempid']);
        if (!empty($employee)) {
            $newEmployee->empid = $_POST['empid'];
        } else {
            array_push($errors, "Employee-id dosn't exist");
        }
        array_push($fields, $newEmployee->empid);
    } else {
        array_push($errors, "Empid cant be blank");
    }

    //checking for errors
    if (empty($errors)) {
        $result = $newEmployee->SelectiveReplace($oldempid, $_POST['empid']);
        if ($result) {
            redirect_to("replaceEmployee.php");
        }
    }
} else {

    //Replace BM details
    if (isset($_POST['submit']) && ($_POST['emptype'] == 'BM')) {
        $newBM = new BM();
        if (!empty($_POST['oldempid'])) {
            $employee = BM::find_by_bmid($_POST['oldempid']);
            if (!empty($employee)) {
                $oldempid = $_POST['oldempid'];
            } else {
                array_push($errors, "BM-id dosn't exist");
            }
        } else {
            array_push($errors, "Old Empid cant be blank");
        }

        if (!empty($_POST['empid'])) {
            $employee = BM::find_by_bmid($_POST['empid']);
            if (!empty($employee)) {
                $newBM->bm_empid = trim($_POST['empid']);
            } else {
                array_push($errors, "BM-id dosn't exist");
            }
            array_push($fields, $newBM->bm_empid);
        } else {
            array_push($errors, "Empid cant be blank");
        }
        //checking for errors
        if (empty($errors)) {
            $result = $newBM->SelectiveReplace($oldempid, $fields);
            if ($result) {
                redirect_to("replaceEmployee.php");
            }
        }
    }
}

//Replace SM details
if (isset($_POST['submit']) && ($_POST['emptype'] == 'SM')) {
    $newSM = new SM();
    if (!empty($_POST['oldempid'])) {
        $employee = SM::find_by_smid($_POST['oldempid']);
        if (!empty($employee)) {
            $oldempid = $_POST['oldempid'];
        } else {
            array_push($errors, "SM-id dosn't exist");
        }
    } else {
        array_push($errors, "Old Empid cant be blank");
    }

    if (!empty($_POST['empid'])) {
        $employee = SM::find_by_smid($_POST['empid']);
        if (!empty($employee)) {
            $newSM->sm_empid = trim($_POST['empid']);
        } else {
            array_push($errors, "SM-id dosn't exist");
        }
        array_push($fields, $newSM->bm_empid);
    } else {
        array_push($errors, "Empid cant be blank");
    }

    //checking for errors
    if (empty($errors)) {
        $result = $newSM->SelectiveReplace($oldempid, $fields);
        if ($result) {
            redirect_to("replaceEmployee.php");
        }
    }
}
$pageTitle = "Replace Employee";
require_once("adminheader.php");
?>

<script>
    function Search() {
        $("#employee").css("background", " url('../images/loader.gif') no-repeat scroll center center ");
        var search_term = $("#type").val() + " " + $("#oldid").val();
        $.post('getEmployeeDetails.php', {search_term: search_term}, function (data) {
            $('#employee').css("background", "#fff");
            $('#employee').html(data);
        });
    }
</script>
<script>
    function Search1() {
        $("#employee1").css("background", " url('../images/loader.gif') no-repeat scroll center center ");
        var search_term = $("#type").val() + " " + $("#1").val();
        $.post('getEmployeeDetails.php', {search_term: search_term}, function (data) {
            $('#employee1').css("background", "#fff");
            $('#employee1').html(data);
        });
    }
</script>
<script>
    function funCalculateTotal(id1) {
        $('#error').empty();
        if ($("#" + id1).val().trim().length == 0) {
            $("#error").append('<li>Please specify Old Empid.</li>');
        }
    }
</script>
<script>
    function funCalculateTotal1(id1) {
        $('#error').empty();
        if ($("#" + id1).val().trim().length == 0) {
            $("#error").append('<li>Please specify  Empid.</li>');
        }
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Replace Employee</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <ul id="error">
        <?php foreach ($errors as $val) { ?>
            <li style="color:red"><?php echo $val; ?></li>
        <?php } ?>
    </ul>
</div>
<div class="row" class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 table-responsive" >
        <form action="replaceEmployee.php" method="post" >
            <table class="table ">
                <tr>
                    <td>For </td>
                    <td>
                        <select name="emptype" id="type" class="form-control">
                            <option value='TM'>TM</option>
                            <option value='BM'>BM</option>
                            <option value='SM'>SM</option>
                    </td>
                    </td>
                </tr>
                <tr>
                    <td>Old Emp-ID</td>
                    <td>
                        <input type="text" class="form-control" name="oldempid" id="oldid" maxlength="30" value="" onchange="funCalculateTotal('oldid')"  />
                    </td>
                    <td>
                        <input type="button"  maxlength="30" value="View" onclick="Search()" class="btn btn-danger btn-xs" />
                    </td>
                </tr>
                <tr>
                <tr>
                    <td>Assign Emp-ID</td>
                    <td>
                        <input type="text" name="empid" maxlength="30" value="" id="1"  onchange="funCalculateTotal1('1')" class="form-control"/>
                    </td>
                    <td>
                        <input type="button"  maxlength="30" value="View" onclick="Search1()"  class="btn btn-danger btn-xs" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" name="submit" value="Save"  class="btn btn-primary"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div  id="employee" class="col-lg-3 col-sm-3 col-md-3 col-xs-7 table-responsive" ></div>
    <div class="col-lg-1 col-md-1 col-xs-1 col-sm-1"></div>
    <div  id="employee1" class="col-lg-3 col-sm-3 col-md-3 col-xs-6 table-responsive" ></div>
</div>
<?php
require_once("adminfooter.php");
