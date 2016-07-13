<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:login.php");
}
require_once( "../includes/initialize.php");
require_once( "../includes/ImageManipulator.php");
require_once( "../includes/class.territory.php");
require_once( "../includes/class.question.php");
require_once( "../includes/class.answer.php");
$pageTitle = 'Profile';
$empid = $_SESSION['SM'];
$Territories = Territory::find_by_empid($empid);
if (isset($_POST['submit'])) {
    $username = $empid;
    $password = $_POST['oldpwd'];
    $newPassword = trim($_POST['newpwd']);
    $found_user = SM::authenticate($username, $password);
    if ($found_user) {
        $changePassword = SM::changePassword($newPassword, $username);
        if ($changePassword) {
            flashMessage("Password Changed Successfully.", 'success');
        }
    } else {
        flashMessage('Old Password Does Not Match', 'error');
    }
}

if (isset($_POST['upload']) && $_POST['upload'] != '') {
    if (isset($_FILES['file']) && $_FILES['file']['name'] != "") {
        //print_r($_FILES);die;
        $allowedExts = array("jpeg", "jpg", 'png', 'gif');
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);
        if ((($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 200000) && in_array(strtolower($extension), $allowedExts)) {
            if ($_FILES["file"]["error"] > 0) {
                
            } else {

                $fileName = time() . ".$extension";

                $manipulator = new ImageManipulator($_FILES['file']['tmp_name']);
                // resizing to 200x200
                $newImage = $manipulator->resample(150, 150);

                $manipulator->save('../files/' . $fileName);

                $fileName = $fileName;

                $Employee = SM::updatepic($empid, $fileName);
            }
        } else {
            flashMessage("Invalid File Type .", 'error');
        }
    }
}
$empName = SM::find_by_smid($empid);
?>
<?php require_once("SMheader.php"); ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $empName->name ?></h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<link href="../css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>
<script src="../js/bootstrap-tagsinput.min.js" type="text/javascript"></script>
<script src="../js/jquery-ui.js" type="text/javascript"></script>
<script>
    function editMobile() {
        $(".mobilehide").show();
        $(".mobileshow").hide();

        $("#submit").click(function () {
            var mobile = $(".mobile").val();
            $.ajax({
                //Send request
                type: 'POST',
                data: {mobile: mobile},
                url: 'edit_mobile.php',
                success: function (data) {
                    $(".result").html(data);
                }
            });
        });
    }

    function editDob() {

        $(".dobhide").show();
        $(".dobshow").hide();

        $("#dob").click(function () {
            var dob = $(".dob").val();
            $.ajax({
                //Send request
                type: 'POST',
                data: {dob: dob},
                url: 'edit_mobile.php',
                success: function (data) {
                    $(".result").html(data).find('[id^=datepicker]').
                            datepicker({
                                dateFormat: 'yy-mm-dd',
                                changeMonth: true,
                                changeYear: true,
                                yearRange: "1901:2014"
                            });
                }
            });
        });
    }

    function editDoa() {

        $(".doahide").show();
        $(".doashow").hide();

        $("#doa").click(function () {
            var doa = $(".doa").val();
            $.ajax({
                //Send request
                type: 'POST',
                data: {doa: doa},
                url: 'edit_mobile.php',
                success: function (data) {
                    $(".result").html(data).find('[id^=datepicker1]').
                            datepicker({
                                dateFormat: 'yy-mm-dd',
                                changeMonth: true,
                                changeYear: true,
                                yearRange: "1901:2014"
                            });
                }
            });
        });
    }
</script>
<style>
    #submit{
        margin-top: 2px;
    }
</style>
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
<style>
    #fileupload{
        display: none;
    }
</style>
<div class ="row">
    <div class="col-lg-3 panel-default">
        <div class=" panel-default">
            <form method="post" enctype="multipart/form-data" action="#" id="updateUser">
                <div class=" panel-body" style="background: #DDD8D8">
                    <div class="thumbnail">
                        <img src="<?php echo isset($empName->profile_photo) && $empName->profile_photo != '' ? '../files/' . $empName->profile_photo : '../files/Default.png' ?>" width="350px" height="400px">
                        <input type="file" name="file" /> 
                        <input type="submit" name="upload" value="Upload" class="btn btn-warning btn-xs">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-9">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#home-pills" data-toggle="tab">Basic Details</a>
            </li>
            <li ><a href="#profile-pills" data-toggle="tab">Change Password</a>
            </li>
            <!--            <li ><a href="#flash-pills" data-toggle="tab">Add Flash Territory</a>
                        </li>-->
            <li ><a href="#question-pills" data-toggle="tab">Ask Question</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active result" id="home-pills" style="margin-top: 1em">
                <div>
                    <img src="">
                </div>
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
            </div>
            <div class="tab-pane fade" id="profile-pills" style="margin-top: 1em">
                <form action="" method="post">
                    <div class="row" style="margin-bottom:1em;">
                        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                            <label>Old Password</label>
                            <input type="text" name="oldpwd" class="form-control" value="<?php
                            if (isset($_POST['oldpwd'])) {
                                echo $_POST['oldpwd'];
                            }
                            ?>" required>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:1em;">
                        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                            <label>New Password</label>
                            <input type="text" name="newpwd" class="form-control" value="" required>

                        </div>
                    </div>
                    <div class="row" style="margin-bottom:1em;">
                        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                            <hr>
                            <input type="submit" name="submit"  class="btn btn-primary btn-large" value="Save">
                        </div>
                    </div>
                </form>

            </div>

            <div class="tab-pane fade" id="question-pills" style="margin-top: 1em">
                <link href="../css/theme-default.css" rel="stylesheet" type="text/css"/>
                <div class="row" >
                    <div class="col-md-12 answerlist" >
<!--                        <div class="row" >
                            <div class="col-md-8">
                                <div class="panel panel-default">
                                    <div class="panel-body" style="padding: 0">
                                        <form id="imageUploadForm" action="../collectQuestionRequest.php" method="post" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <textarea class="form-control" placeholder="Ask Question" name="question" style="width: 502px"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12" style="padding-bottom: 5px">
                                                    <div class=" pull-left">
                                                        <button type="button" class="btn btn-default" onclick="upload()" style="padding:0px;color: #0088cc;border-color: #fff; "><i class="fa fa-2x fa-camera"></i></button>
                                                        <input type="file" name="file" id="fileupload"/>
                                                        <input type="hidden" name="profile" value="yes">
                                                        <label id="uploadValue"></label>
                                                    </div>
                                                    <div class="pull-right">
                                                        <button class="btn btn-danger btn-sm"  type="submit" name="ask_question" id="ask"><span class="fa fa-share"></span> POST</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <div class="timeline timeline-right">
                            <?php
                            $Questions = Question::find_by_empid($empid);
                            if (!empty($Questions)) {
                                foreach ($Questions as $Question) {
                                    $Answers = Answer::find_by_qtn_id($Question->id);
                                    //$Employee = Employee::find_by_empid($Question->empid);
                                    $output = Question::Timeline($Questions);
                                    echo $output;
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $("#addTerritory").click(function () {
            var addRow = 'Yes';
            $.ajax({
                //Send request
                type: 'POST',
                data: {addRow: addRow},
                url: 'getNewRow.php',
                success: function (data) {
                    $(".flash").append(data);
                    var main = $(".main").length;
                    var sub = $(".sub").length;


                    $(".sub:last").attr("name", 'sub[]');
                    $(".sub:last").attr("data-role", 'tagsinput');
                    $(".main:last").attr("name", 'main[]');
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function (e) {
        $('#imageUploadForm').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    //alert(data);
                    $(".answerlist").html(data);
                },
                error: function (data) {
                    console.log("error");
                    console.log(data);
                }
            });
        }));
    });


    function addComment(id) {
        var qtn_id = id;
        var answer = $("#" + qtn_id).closest('.comment-write').find('.question').val();

        $.ajax({
            type: 'POST',
            url: '../collectQuestionRequest.php',
            data: {qtn_id: qtn_id, answer: answer},
            success: function (data) {
                $("." + qtn_id).html(data);
            },
            error: function (data) {
                console.log("error");
                console.log(data);
            }
        });
    }

    function upload() {
        document.getElementById("fileupload").click();
        $("#fileupload").change(function () {
            var filename = $('input[type=file]').val().split('\\').pop();
            $("#uploadValue").html(filename);
        });
    }
</script>
<?php require_once("SMfooter.php"); ?>
