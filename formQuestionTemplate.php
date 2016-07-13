<div class="row" >
    <div class="<?php
    if (isset($_POST['profile'])) {
        echo 'col-md-9';
    } else {
        echo 'col-md-9';
    }
    ?>">
        <div class="panel panel-default">
            <div class="panel-body " style="padding: 0">
                <form id="imageUploadForm " action="<?php
                if (isset($_SESSION['BM']) || isset($_SESSION['SM'])) {
                    echo '../collectQuestionRequest.php';
                } else {
                    echo 'collectQuestionRequest.php';
                }
                ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <textarea class="form-control" placeholder="Ask Question" name="question" style="width: 100%" required></textarea>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12" style="padding-bottom: 5px">
                            <div class=" pull-left">
                                <button type="button" class="btn btn-default" onclick="upload()" style="padding:0px;color: #0088cc;border-color: #fff; "><i class="fa fa-2x fa-camera"></i></button>
                                <input type="file" name="file" id="fileupload"/>
                                <?php if (isset($_POST['profile'])) { ?>
                                    <input type="hidden" name="profile" value="yes">

                                <?php } ?>
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