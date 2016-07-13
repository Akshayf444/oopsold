<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
require_once(dirname(__FILE__) . "/includes/class.activity_master.php");
$empid = $_SESSION['employee'];

$Activities = ActivityMaster::find_all();
$output = '<option>Select Activity</option>';
foreach ($Activities as $Activity) {
    $output .='<option value ="' . $Activity->id . '" >' . $Activity->activity . '</option>';
}

$today = !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d', time());
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 1;
$total_count = (int)date('t',  strtotime($today)) - (int)date("d", strtotime($today)) + 3;
//echo $total_count;
$pagination = new Pagination($page, $per_page, $total_count);
$Plannings = Planning::entryExist($today, $empid);
if (isset($Plannings->date)) {
    $date = date('d-m-Y', strtotime($Plannings->date));
} else {
    $date = date('d-m-Y', strtotime($today));
    $date .= "<br/><small>Monthly Planning Dosnt Exist For This Date</small>";
}

/* * ************************ Save Daily Planning **************************** */
if (isset($_POST['saveDailyPlanning'])) {

    $daily_plan = new DailyCallPlanning();
    for ($i = 0; $i < count($_POST['docid']); $i++) {
        $daily_plan->docid = $_POST['docid'][$i];
        $daily_plan->input = $_POST['inputs'][$i];
        $daily_plan->plan_id = $_POST['plan_id'][$i];

        $daily_plan->service = $_POST['services'][$i];
        $daily_plan->pob = $_POST['pob'][$i];
        $daily_plan->post_call_planning = $_POST['post_call_planning'][$i];
        $daily_plan->meet = $_POST[$daily_plan->docid . 'radio'];
        $daily_plan->reason = $_POST['reason'][$i];
        $daily_plan->created = date('Y-m-d', time());
        $daily_plan->create();
    }

    redirect_to('Daily_plan.php?page=' . $page . '&plan_id=' . $daily_plan->plan_id);
}

/* * ************************* Save Doctor Visits *************************** */
if (isset($_POST['submit']) && isset($_POST['doctors'])) {
    $_SESSION['visit_ids'] = array();
    $trimmed_array = array_filter(array_map('trim', $_POST['doctors']));
    $newVisit = new DoctorVisit();
    foreach ($trimmed_array as $docid) {
        $newVisit->docid = $docid;
        $newVisit->empid = $_SESSION['employee'];
        $newVisit->created = date('Y-m-d', time());
        $newVisit->plan_id = $_POST['plan_id'];
        $result = $newVisit->create();
        array_push($_SESSION['visit_ids'], $result);
    }
    redirect_to('Daily_plan.php?plan_id=' . $newVisit->plan_id);
}

/* * ****************************** Edit Daily Plan ************************** */
if (isset($_POST['editDailyPlan'])) {

    $daily_plan = new DailyCallPlanning();
    $daily_plan->id = $_POST['id'];
    $daily_plan->docid = $_POST['docid'];
    $daily_plan->input = $_POST['inputs'];
    $daily_plan->plan_id = $_POST['plan_id'];
    $daily_plan->service = $_POST['services'];
    $daily_plan->pob = $_POST['pob'];
    $daily_plan->pob = $_POST['reason'];
    $daily_plan->meet = $_POST['meet'];
    $daily_plan->post_call_planning = $_POST['post_call_planning'];
    $daily_plan->update();
    flashMessage("Record Updated Successfully .", 'success');
    redirect_to('Daily_plan.php?page=' . $page . '&plan_id=' . $_POST['plan_id']);
}

require_once("layouts/TMheader.php");
?>
<style>
    label{
        margin-bottom: 0px;
    }

    .toggle {
        margin:4px;
        background-color:#EFEFEF;
        border-radius:4px;
        border:1px solid #EFEFEF;
        overflow:auto;
        float:left;

    }

    .toggle label {
        float:left;
        width:2.0em;

    }

    .toggle label span {
        text-align:center;
        padding:3px 0px;
        display:block;
        cursor: pointer;

        // margin-top: -25px;
    }

    .toggle label input {
        visibility: hidden;
        position:absolute;
        top:-20px;
    }

    .toggle .input-checked {
        background-color:#000;
        color:red;
        -webkit-transition: 0.15s ease-out;
        -moz-transition: 0.15s ease-out;
        -o-transition: 0.15s ease-out;
        transition: 0.15s ease-out;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">View Daily Planning</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class ="row">
    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            if ($pagination->has_previous_page()) {
                echo "<div class='col-lg-1'> <a class ='btn btn-default' href=\"Daily_plan.php?page=";
                echo $pagination->previous_page();
                echo '&date=';
                $no_of_days = "+" . $page - 2 . " days";
                echo date('Y-m-d', strtotime($no_of_days));
                echo "\">&laquo; Previous</a></div> ";
            }

            echo "<div class='col-lg-6 col-lg-push-4'><h3>" . $date . "</h3></div>";

            if ($pagination->has_next_page()) {
                echo "<div class ='col-lg-1 pull-right'> <a class ='btn btn-default' href=\"Daily_plan.php?page=";
                echo $pagination->next_page();
                echo '&date=';
                $no_of_days = "+" . $page  . " days";
                echo date('Y-m-d', strtotime($no_of_days));
                echo "\">Next &raquo;</a> </div>";
            }
        }
        ?>
    </div>
</div>
<div class="row">
    <form action="" method="POST">
        <div class="col-md-12 col-lg-12">
            <?php
            if (isset($_GET['plan_id']) || isset($Plannings->id)) {
                if (isset($_GET['plan_id'])) {
                    $plan_id = $_GET['plan_id'];
                } else {
                    $plan_id = $Plannings->id;
                }

                $visits = DoctorVisit::find_by_id($plan_id);
                if (!empty($visits)) {
                    $entryExist = DailyCallPlanning::find_by_plan_id($plan_id);
                    if (empty($entryExist)) {
                        foreach ($visits as $VisitDetails) {
                            $Planning = Planning::find_by_id($VisitDetails->plan_id);
                            $date = date('d-m-Y', strtotime($Planning->date));
                            $basicProfile = BasicProfile::find_by_docid($VisitDetails->docid);
                            $speciality = isset($VisitDetails->speciality) ? $VisitDetails->speciality : '';
                            $msl_code = isset($basicProfile->msl_code) ? "MSL Code :" . $basicProfile->msl_code : "";
                            ?>

                            <div class="panel panel-default" style="border-radius: 0px;">
                                <div class="panel-heading " style="background: #fff;  border-bottom: 2px solid #21CC00;">
                                    <a>
                                        <h4 class="panel-title">
                                            <?php
                                            echo "<b>" . $VisitDetails->name . '</b><span class="pull-right"><span style="margin-right:20px">' . $msl_code . '</span><i class="glyphicon glyphicon-plus" ></i></span></h4>';
                                            $lastMeet = DailyCallPlanning::lastMeet($VisitDetails->docid);
                                            if (!empty($lastMeet)) {
                                                echo '<small>Last Meet :' . date('d-m-Y', strtotime($lastMeet->created)) . '</small>';
                                            }
                                            ?>

                                    </a>
                                </div>
                                <div class="panel-body">
                                    <dl class="dl-horizontal">
                                        <dt style="width: auto;">Priority Product</dt>
                                        <dd ><?php
                                            $Product = Product::find_by_id($VisitDetails->product1_id);
                                            echo $Product->name . ", ";
                                            $Product = Product::find_by_id($VisitDetails->product2_id);
                                            echo $Product->name . ", ";
                                            $Product = Product::find_by_id($VisitDetails->product3_id);
                                            echo $Product->name;
                                            ?></dd>
                                        <?php
                                        if (isset($basicProfile->msl_code)) {
                                            echo '<dt style="width: auto;">Address</dt>                                <dd><address>';
                                            echo isset($basicProfile->clinic_name) ? "" . "<strong>" . $basicProfile->clinic_name . "</strong><br/>" : "";
                                            $address1 = array($basicProfile->plot1, $basicProfile->street1, $basicProfile->area1, $basicProfile->city1, $basicProfile->state1, $basicProfile->pincode1);
                                            $trimmed_array1 = array_filter(array_map('trim', $address1));
                                            $trimmed_array1 = join(",", $trimmed_array1);
                                            echo $trimmed_array1 . '<br/></address></dd>';
                                        }
                                        ?>
                                        <dt style="width: auto;">Meet</dt>
                                        <dd style="background: #EFEFEF;  width: 76px;">
                                            <div class="toggle">
                                                <label><input type="radio" name="<?php echo $VisitDetails->docid . 'radio'; ?>" value="Yes"><span id="<?php echo $VisitDetails->docid . "-1"; ?>">Yes</span></label>    
                                            </div>
                                            <div class="toggle">
                                                <label><input type="radio" name="<?php echo $VisitDetails->docid . 'radio'; ?>" value="No"><span id="<?php echo $VisitDetails->docid . "-2"; ?>" >No</span></label>
                                            </div>
                                        </dd>
                                    </dl>

                                    <div id="<?php echo "heading" . $VisitDetails->docid; ?>" class="custom-collapse " style="display: none">
                                        <div class="row row-margin-top">
                                            <div class="col-lg-4"><input type="text" class="form-control" name="inputs[]" placeholder="Input">
                                                <input type="hidden" name="docid[]" value="<?php echo $VisitDetails->docid; ?>">
                                                <input type="hidden" name="plan_id[]" value="<?php echo $VisitDetails->plan_id; ?>">
                                            </div>  
                                            <div class="col-lg-4"><input type="text" class="form-control" name="pob[]" placeholder="POB Collected."></div>
                                            <div class="col-lg-4"><input type="text" class="form-control" name="services[]" placeholder="Services">  </div> 
                                        </div> 
                                        <div class="row row-margin-top">
                                            <div class="col-lg-12"><textarea class="form-control" name="post_call_planning[]" placeholder="Post Call Planning"></textarea> </div> 
                                        </div> 
                                    </div>
                                    <div id="<?php echo "reason" . $VisitDetails->docid; ?>" class="custom-collapse " style="display: none">
                                        <div class="row row-margin-top">
                                            <div class="col-lg-12"><textarea class="form-control" name="reason[]" placeholder="Reason"></textarea> </div> 
                                        </div> 
                                    </div>
                                </div>                            
                            </div>
                            <?php
                        }
                        echo '<div class="row"><div class="col-lg-12">
                                            <input type="submit" name="saveDailyPlanning" class="btn btn-primary" value="Save">
                                        </div></div>';
                    } else {
                        $DailyCallPlanExist = DailyCallPlanning::find_by_plan_id2($plan_id);
                        if (!empty($DailyCallPlanExist)) {
                            foreach ($DailyCallPlanExist as $daily_plan) {
                                $basicProfile = BasicProfile::find_by_docid($daily_plan->docid);
                                $speciality = isset($basicProfile->speciality) ? $basicProfile->speciality : '';
                                $msl_code = isset($basicProfile->msl_code) ? "MSL Code :" . $basicProfile->msl_code : "";
                                ?>
                                <div class="panel-group" >
                                    <div class="panel panel-default panel-faq" style="border-radius: 0px;">
                                        <div class="panel-heading " style="background: #fff;  border-bottom: 2px solid #21CC00;">
                                            <a data-toggle="collapse" data-parent="#<?php echo $daily_plan->docid; ?>" href="#<?php echo "heading" . $daily_plan->docid; ?>" id="<?php echo $daily_plan->docid; ?> "  >

                                                <h4 class="panel-title">
                                                    <?php echo "<b>" . $daily_plan->name . '</b><span class="pull-right"><span style="margin-right:20px">' . $msl_code . '</span><i class="glyphicon glyphicon-plus" ></i></span>'; ?>
                                                </h4>

                                            </a>
                                        </div>
                                        <div class="panel-body">
                                            <dl class="dl-horizontal">
                                                <dt style="width: auto;">Priority Product</dt>
                                                <dd ><?php
                                                    $Product = Product::find_by_id($daily_plan->product1_id);
                                                    echo $Product->name . ", ";
                                                    $Product = Product::find_by_id($daily_plan->product2_id);
                                                    echo $Product->name . ", ";
                                                    $Product = Product::find_by_id($daily_plan->product3_id);
                                                    echo $Product->name;
                                                    ?></dd>
                                                <?php
                                                if (isset($basicProfile->msl_code)) {
                                                    echo '<dt style="width: auto;">Address</dt><dd><address>';
                                                    echo isset($basicProfile->clinic_name) ? "" . "<strong>" . $basicProfile->clinic_name . "</strong><br/>" : "";
                                                    $address1 = array($basicProfile->plot1, $basicProfile->street1, $basicProfile->area1, $basicProfile->city1, $basicProfile->state1, $basicProfile->pincode1);
                                                    $trimmed_array1 = array_filter(array_map('trim', $address1));
                                                    $trimmed_array1 = join(",", $trimmed_array1);
                                                    echo $trimmed_array1 . '<br/></address></dd>';
                                                }
                                                ?>

                                            </dl>
                                            <div id="<?php echo "heading" . $daily_plan->docid; ?>"  style="display: none">
                                                <?php if ($daily_plan->meet === 'Yes') { ?>
                                                    <div class="col-lg-12">
                                                        <div class="pull-right"><button type="button" class="btn btn-danger btn-xs "  onclick="sendAjaxRequest(this.id)" id="<?php echo $daily_plan->id; ?>" style="margin-bottom: 2px"><i class="fa fa-edit" ></i></button></div>
                                                    </div>

                                                    <div class="row row-margin-top">
                                                        <div class="col-lg-4"><input type="text" class="form-control"  placeholder="Input" value="<?php echo $daily_plan->input; ?>"></div>  
                                                        <div class="col-lg-4"><input type="text" class="form-control"  placeholder="POB Collected." value="<?php echo $daily_plan->pob; ?>"></div>
                                                        <div class="col-lg-4"><input type="text" class="form-control"  placeholder="Services" value="<?php echo $daily_plan->service; ?>">  </div> 
                                                    </div> 
                                                    <div class="row row-margin-top">
                                                        <div class="col-lg-12"><textarea class="form-control"  placeholder="Post Call Planning" ><?php echo $daily_plan->post_call_planning; ?></textarea> </div> 
                                                    </div> 
                                                <?php } else { ?>
                                                    <div class="row row-margin-top">
                                                        <div class="col-lg-12"><textarea class="form-control"  placeholder="Reason"><?php echo $daily_plan->reason; ?></textarea> </div> 
                                                    </div> 
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                } else {
                    echo "<div class = 'center-block' style='width:50%'>Doctor Visit Details Not Found For this Date. Click Here to add details.<input type='button' value='Add' class='btn btn-primary btn-xs' id='" . $Plannings->id . "' data-area='" . $Plannings->area . "' onclick ='addDoctorVisit(this.id)'></div>";
                }
            }
            ?>

        </div>
    </form>
</div>

<div id="modalpopup"></div>
<?php require_once("layouts/TMfooter.php"); ?>
<script type="text/javascript">

    $(document).ready(function () {
        $('label').click(function () {
            $(this).children('span').addClass('input-checked');
            $(this).parent('.toggle').siblings('.toggle').children('label').children('span').removeClass('input-checked');

            var id = $(this).children('span').attr('id').split("-");
            id = id[0];

            if ($(this).children('span').text() === 'Yes') {
                $("#heading" + id).show();
                $("#reason" + id).hide();
            } else if ($(this).children('span').text() === 'No') {
                $("#heading" + id).hide();
                $("#reason" + id).show();
            }
        });
    });

    function addDoctorVisit(id) {
        var id = id;
        var area = $('#' + id).attr('data-area');
        $.ajax({
            //Send request
            type: 'POST',
            data: {id: id, area: area},
            url: 'update_daily_plan.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    }

    $('a').click(function () {
        var id = $(this).attr('id');
        $('#heading' + id).toggle();
    });
    function sendAjaxRequest(id) {
        var callid = id;
        $.ajax({
            //Send request
            type: 'POST',
            data: {id: callid},
            url: 'edit_daily_planning.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    }
</script>