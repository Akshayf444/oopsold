<div class="row" >
    <div class="col-lg-12 ">
        <div class="timeline timeline-right " id="question-list">
            <?php
            if (isset($_POST['profile'])) {
                $Questions = Question::find_by_empid($newQuestion->empid);
            } else {                
                $Questions = Question::find_all();
            }
            $output = Question::Timeline($Questions);
            echo $output;
            ?>
        </div>
    </div>
</div>