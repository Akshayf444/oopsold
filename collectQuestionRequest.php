<?php
session_start();
require_once(dirname(__FILE__) . "/includes/initialize.php");
require_once(dirname(__FILE__) . "/includes/class.question.php");
require_once(dirname(__FILE__) . "/includes/class.answer.php");

if (isset($_POST['ask_question'])) {
    $errors = array();
    $fileName = '';
    $newQuestion = new Question();
    $newQuestion->question = $_POST['question'];
    $newQuestion->created = strftime("%Y-%m-%d %H:%M:%S", time());
    if (isset($_SESSION['employee'])) {
        $newQuestion->empid = $_SESSION['employee'];
        $newQuestion->emp_type = 'tm';
    } elseif (isset($_SESSION['BM'])) {
        $newQuestion->empid = $_SESSION['BM'];
        $newQuestion->emp_type = 'bm';
    } elseif (isset($_SESSION['SM'])) {
        $newQuestion->empid = $_SESSION['SM'];
        $newQuestion->emp_type = 'sm';
    }


    if (isset($_FILES['file']) && $_FILES['file']['name'] != "") {
        //print_r($_FILES);die;
        $allowedExts = array("jpeg", "jpg", 'png', 'Png');
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);
        if ((($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 2000000000) && in_array($extension, $allowedExts)) {
            if ($_FILES["file"]["error"] > 0) {
                
            } else {
                $fileName = time() . ".$extension";
                $manipulator = new ImageManipulator($_FILES['file']['tmp_name']);
                $newImage = $manipulator->resample(150, 150);
                $manipulator->save('posts/' . $fileName);

                $fileName = $fileName;
                $newQuestion->type = 'image';
                $newQuestion->filename = $fileName;
                $max_file_size = 1048576;
            }
        } else {
            array_push($errors, 'Invalid File');
            flashMessage("Invalid File Type .", 'error');
        }
    } else {
        $newQuestion->type = '';
        $newQuestion->filename = '';
    }

    if (empty($errors)) {
        $newQuestion->create();
    }

    require_once(dirname(__FILE__) . "/askQuestionTemplate.php");
    
} elseif (isset($_POST['qtn_id']) && isset($_POST['answer']) && $_POST['answer'] != '') {
    $qtn_id = $_POST['qtn_id'];
    $newAnswer = new Answer();
    $newAnswer->qtn_id = $_POST['qtn_id'];
    $newAnswer->created = strftime("%Y-%m-%d %H:%M:%S", time());

    if (isset($_SESSION['employee'])) {
        $newAnswer->empid = $_SESSION['employee'];
        $newAnswer->emp_type = 'tm';
    } elseif (isset($_SESSION['BM'])) {
        $newAnswer->empid = $_SESSION['BM'];
        $newAnswer->emp_type = 'bm';
    } elseif (isset($_SESSION['SM'])) {
        $newAnswer->empid = $_SESSION['SM'];
        $newAnswer->emp_type = 'sm';
    }

    $newAnswer->answer = $_POST['answer'];
    $newAnswer->create();

    $Answers = Answer::find_by_qtn_id($newAnswer->qtn_id);
    if (!empty($Answers)) {
        foreach ($Answers as $Answer) {
            if ($Answer->emp_type == 'tm') {
                $Employee = Employee::find_by_empid($Answer->empid);
            } elseif ($Answer->emp_type == 'bm') {
                $Employee = BM::find_by_bmid($Answer->empid);
            } elseif ($Answer->emp_type == 'sm') {
                $Employee = SM::find_by_smid($Answer->empid);
            }
            ?>
            <div class="comment-item">
                <img src="<?php echo $GLOBALS['site_root']; ?>/files/<?php echo isset($Employee->profile_photo) && $Employee->profile_photo != '' ? $Employee->profile_photo : "user.png"; ?>"/> 
                <p class="comment-head">
                    <a href="#"><?php echo $Employee->name ?></a> <span class="text-muted"><?php echo time_passed(strtotime($Answer->created)); ?></span>
                </p>
                <p><?php echo $Answer->answer; ?></p>
            </div>
            <?php
        }
    }
    ?>
    <div class="comment-write">                                                
        <textarea class="form-control question" placeholder="Write a comment" rows="1"></textarea>
        <div class="pull-right " style="margin-top: 2px"><input type="button" class="btn btn-success btn-xs addAnswer" id="<?php echo $qtn_id; ?>"  value="post" onclick="addComment(this.id)"></div>
    </div>
    <script>
        function addComment(id) {
            var qtn_id = id;
            var answer = $("#" + qtn_id).closest('.comment-write').find('.question').val();
            $.ajax({
                type: 'POST',
                url: '<?php
    if (isset($_SESSION['BM']) || isset($_SESSION['SM'])) {
        echo '../collectQuestionRequest.php';
    } else {
        echo 'collectQuestionRequest.php';
    }
    ?>',
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
    <?php
} elseif (isset($_POST['group_no'])) {
    $group_number = (int) $_POST['group_no'];
    $position = ($group_number * 25);
    $Questions = Question::find_all($position);
    if (!empty($Questions)) {
        require_once(dirname(__FILE__) . "/QuestionListTemplate.php");
    }
} elseif (isset($_POST['delete_qtn']) && isset($_POST['qtn_id'])) {
    $deleteQuestion = new Question();
    $deleteQuestion->id = trim($_POST['qtn_id']);
    $deleteQuestion->delete();
    $Questions = Question::find_all();
    require_once(dirname(__FILE__) . "/QuestionListTemplate.php");
}
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>