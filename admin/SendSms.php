<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$final_number_list = array();
$errors = array();
if (isset($_POST['submit']) && ($_POST['emptype']) == 'All') {
    if (preg_match("/^\d{10}(,\d{10})*$/", $_POST['mobile'])) {
        $extraNumbers = array();
        $extraNumbers = explode(",", $_POST['mobile']);
        $message = $_POST['message'];

        $SMnumbers = SM::allNumbers();
        $BMnumbers = BM::allNumbers();
        $Employeenumbers = Employee::allNumbers();
        $final1 = array_merge_recursive($extraNumbers, $SMnumbers);
        $final2 = array_merge_recursive($final1, $BMnumbers);
        $final3 = array_merge_recursive($final2, $Employeenumbers);
        $final4 = array_unique($final3);
        foreach ($final4 as $number) {
            sendsms($number, $message);
        }
    } else {
        array_push($errors, "Invalid mobile no entered.");
    }
}
if (isset($_POST['submit']) && ($_POST['emptype']) == 'TM') {
    if (preg_match("/^\d{10}(,\d{10})*$/", $_POST['mobile'])) {
        $extraNumbers = array();
        $extraNumbers = explode(",", $_POST['mobile']);
        $message = $_POST['message'];
        $Employeenumbers = Employee::allNumbers();
        $final1 = array_merge_recursive($extraNumbers, $Employeenumbers);
        $final4 = array_unique($final1);
        foreach ($final4 as $number) {
            sendsms($number, $message);
        }
    } else {
        array_push($errors, "Invalid mobile no entered.");
    }
}
if (isset($_POST['submit']) && ($_POST['emptype']) == 'BM') {
    if (preg_match("/^\d{10}(,\d{10})*$/", $_POST['mobile'])) {
        $extraNumbers = array();
        $extraNumbers = explode(",", $_POST['mobile']);
        $message = $_POST['message'];
        $BMnumbers = BM::allNumbers();
        $final1 = array_merge_recursive($extraNumbers, $BMnumbers);
        $final4 = array_unique($final1);
        foreach ($final4 as $number) {
            sendsms($number, $message);
        }
    } else {
        array_push($errors, "Invalid mobile no entered.");
    }
}
if (isset($_POST['submit']) && ($_POST['emptype']) == 'SM') {
    if (preg_match("/^\d{10}(,\d{10})*$/", $_POST['mobile'])) {
        $extraNumbers = array();
        $extraNumbers = explode(",", $_POST['mobile']);
        $message = $_POST['message'];
        $SMnumbers = SM::allNumbers();
        $final1 = array_merge_recursive($extraNumbers, $SMnumbers);
        $final4 = array_unique($final1);
        foreach ($final4 as $number) {
            sendsms($number, $message);
        }
    } else {
        array_push($errors, "Invalid mobile no entered.");
    }
}

require_once("adminheader.php");
?>

<script language="javascript" type="text/javascript">
    $(document).ready(function () {
        var $remaining = $('#remaining'),
                $messages = $remaining.next();


        $('.IDmsgCount').keyup(function () {
            var chars = this.value.length,
                    messages = Math.ceil(chars / 160),
                    remaining = messages * 160 - (chars % (messages * 160) || messages * 160);

            $remaining.text(remaining + ' characters remaining');
            $messages.text(messages + ' message(s)');
        });
    });
</script>
<script language="javascript" type="text/javascript">
    function mobileValidation() {
        var pattern = /^\d{10}(,\d{10})*$/;
        if (!(pattern.test($("#multipleNumbers").val()))) {
            $("#error").empty();
            $("#error").append('<li>Invalid Mobile No.</li>');
        }

    }
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Send SMS</h1>
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
<div class="col-lg-7 col-sm-7 col-md-7 col-xs-7">

    <form action="SendSms.php" method="post">
        <table class="table">
            <tr>
                <td>Send SMS to </td>
                <td>
                    <select name="emptype" id="type" class="form-control">
                        <option value='All'>All</option>
                        <option value='TM'>TM</option>
                        <option value='BM'>BM</option>
                        <option value='SM'>SM</option>
                </td>
            </tr>
            <tr>
                <td>Mobile No</td>
                <td>
                    <input type="text" name="mobile"  value="" onchange="mobileValidation()" id="multipleNumbers"  class="form-control"/>
                </td>
            </tr>
            <tr>
                <td>Message</td>
                <td>
                    <textarea name="message" cols="30" rows="4" class="IDmsgCount" class="form-control" style="width:100%"></textarea>
                    <strong>  <span id="remaining">160 characters remaining</span> <span id="messages">1 message(s)</span></strong>		
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="submit" value="Send Sms" class="btn btn-primary" />
                </td>
            </tr>
        </table>
    </form>
</div>
<?php require_once("adminfooter.php"); ?>