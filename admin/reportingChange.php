<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$errors = array();
$fields = array();

if (isset($_POST['submit']) && ($_POST['emptype'] == 'TM')) {
    $newEmployee = new Employee();
    if (!empty($_POST['empid'])) {
        $employee = BM::find_by_bmid($_POST['empid']);
        if (!empty($employee)) {
            $reporting_empid = trim($_POST['empid']);
        } else {
            array_push($errors, "BM-id dosn't exist");
        }
    } else {
        array_push($errors, "Empid cant be blank");
    }

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

    if (empty($errors)) {
        $result = $newEmployee->reportingChange($reporting_empid, $oldempid);
        if ($result) {
            echo "<script>alert('Updated Succesfully');</script>";
        }
    }
}

if (isset($_POST['submit']) && ($_POST['emptype'] == 'BM')) {
    $newBM = new BM();

    if (!empty($_POST['empid'])) {
        $employee = SM::find_by_smid($_POST['empid']);
        if (!empty($employee)) {
            $reporting_empid = trim($_POST['empid']);
        } else {
            array_push($errors, "SM-id dosn't exist");
        }
        array_push($fields, $reporting_empid);
    } else {
        array_push($errors, "Empid cant be blank");
    }

    if (!empty($_POST['oldempid'])) {
        $employee = BM::find_by_bmid($_POST['oldempid']);
        if (!empty($employee)) {
            $oldempid = trim($_POST['oldempid']);
        } else {
            array_push($errors, "BM-id dosnt exist");
        }

        array_push($fields, $oldempid);
    } else {
        array_push($errors, "Old Empid cant be blank");
    }

    if (empty($errors)) {
        $result = $newBM->reportingChange($fields);
        if ($result) {
            echo "<script>alert('Updated Succesfully');</script>";
        }
    }
}

$pageTitle = "Reporting Change";
require_once("adminheader.php");
?>

<script>
//Getting data on click of button
    function Search() {
        var search_term = $("#type").val() + " " + $("#oldid").val();
        $.post('getReportingDetail.php', {search_term: search_term}, function (data) {
            $('#animation').hide();
            $('#employee').html(data);
        });
    }
</script>
<script>
//Getting data on click of button
    function Search1() {
        var search_term = $("#change").val() + " " + $("#assignId").val();
        $.post('getReportingDetail.php', {search_term: search_term}, function (data) {
            $('#animation').hide();
            $('#employee1').html(data);
        });
    }
</script>
<script>
//changing menu according to TM and BM
    function ChangeMenu() {
        var search_term = $("#type").val();
        $.post('getSM.php', {search_term: search_term}, function (data) {
            $('#type2').html(data);
        });
    }
</script>
<script>
//validations
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
        <h1 class="page-header">Reporting Change</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <ul id="error" style="list-style-type: none">
        <?php foreach ($errors as $val) { ?>
            <li style="color:red"><?php echo $val; ?></li>
        <?php } ?>
    </ul>
</div>

<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 table-responsive">
        <form action="reportingChange.php" method="post" >
            <table class="table table-bordered">
                <tr>
                    <td>For </td>
                    <td colspan="2">
                        <select name="emptype" id="type" onchange="ChangeMenu()" class="form-control">
                            <option value='TM'>TM</option>
                            <option value='BM'>BM</option>
                        </select>
                    </td>

                </tr>
                <tr>
                    <td>Old Emp-ID</td>
                    <td>
                        <input type="text" name="oldempid" id="oldid" maxlength="30" value="" onchange="funCalculateTotal('oldid')"  class="form-control"/>
                    </td>
                    <td>
                        <input type="button"  maxlength="30" value="View" onclick="Search()" class="btn btn-danger btn-xs"/>
                    </td>
                </tr>
                <tr>
                    <td>For </td>
                    <td id="type2" name="report" colspan="2">
                        <select id="change" disabled class="form-control">
                            <option value='BM'>BM</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Assign Emp-ID</td>
                    <td>
                        <input type="text" class="form-control" name="empid" maxlength="30" value="" id="assignId" onchange="funCalculateTotal1('assignId')"/>
                    </td>
                    <td>
                        <input type="button"  maxlength="30" value="View" onclick="Search1()" class="btn btn-danger btn-xs"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" name="submit" value="Save" class="btn btn-primary"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 table-responsive" id="employee"></div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 table-responsive" id="employee1"></div>
</div>
<?php require_once("adminfooter.php"); ?>