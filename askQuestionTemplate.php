<?php
require_once(dirname(__FILE__) . "/includes/class.question.php");
require_once(dirname(__FILE__) . "/includes/class.answer.php");

$total_records = Question::count_all();
$total_groups = ceil($total_records / 25);
$_SESSION['total_groups'] = $total_groups;
if (isset($_SESSION['BM']) || isset($_SESSION['SM'])) {
    echo '<link href="../css/theme-default.css" rel="stylesheet" type="text/css"/><script src="../js/ajaxLoader2.js" type="text/javascript"></script>';
} else {
    echo '<link href="css/theme-default.css" rel="stylesheet" type="text/css"/><script src="js/ajaxLoader2.js" type="text/javascript"></script>';
}
?>
<style>
    #fileupload{
        display: none;
    }
</style>
<div class="row" >
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body " style="padding: 0">
                <form id="imageUploadForm" action="<?php
                if (isset($_SESSION['BM']) || isset($_SESSION['SM'])) {
                    echo '../collectQuestionRequest.php';
                } else {
                    echo 'collectQuestionRequest.php';
                }
                ?>" 
                      method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <textarea class="form-control" placeholder="Ask Question" name="question" style="width: 100%" required></textarea>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12" style="padding-bottom: 5px">
                            <div class=" pull-left">
                                <button type="button" class="btn btn-default" onclick="upload()" style="padding:0px;color: #0088cc;border-color: #fff; "><i class="fa fa-2x fa-camera"></i></button>
                                <input type="file" name="file" id="fileupload"/>
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
</div>
<div class="row" >
    <div class="col-lg-12 ">
        <div class="timeline timeline-right " id="question-list">

        </div>
    </div>
</div>
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

    function deleteAnswer(id) {
        var qtn_id = id.replace('qtn', '');
        //alert(qtn_id);
        $.ajax({
            type: 'POST',
            url: '<?php
                      if (isset($_SESSION['BM']) || isset($_SESSION['SM'])) {
                          echo '../collectQuestionRequest.php';
                      } else {
                          echo 'collectQuestionRequest.php';
                      }
                ?>',
            data: {qtn_id: qtn_id, delete_qtn: 'yes'},
            success: function (data) {
                $("#question-list").html(data);
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
<script>
    $(document).ready(function () {
        var track_load = 0;
        var loading = false;
        var total_groups = <?php echo $_SESSION['total_groups']; ?>;

        $('#question-list').load('<?php
                      if (isset($_SESSION['BM']) || isset($_SESSION['SM'])) {
                          echo '../collectQuestionRequest.php';
                      } else {
                          echo 'collectQuestionRequest.php';
                      }
                ?>', {'group_no': track_load}, function () {
            track_load++;
        });

        $(window).scroll(function () {

            if ($(window).scrollTop() + $(window).height() == $(document).height())
            {

                if (track_load <= total_groups && loading == false) {
                    $.post('<?php
                      if (isset($_SESSION['BM']) || isset($_SESSION['SM'])) {
                          echo '../collectQuestionRequest.php';
                      } else {
                          echo 'collectQuestionRequest.php';
                      }
                ?>', {'group_no': track_load}, function (data) {
                        if (data.trim() != '') {
                            $("#question-list").append(data);
                        } else {
                            $(".loadmore").hide();
                        }

                        track_load++;

                    }).fail(function (xhr, ajaxOptions, thrownError) {
                        alert("error");

                    });

                }
            }
        });

    });
</script>