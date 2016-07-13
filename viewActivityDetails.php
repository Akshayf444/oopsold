<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];

$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 1;
$total_count = Activity::count_by_empid($empid);
$pagination = new Pagination($page, $per_page, $total_count);

$sql = "SELECT * FROM activity_details WHERE empid = '$empid' ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";

$Activity = Activity::find_by_sql($sql);
$Activity = !empty($Activity) ? array_shift($Activity) : FALSE;

$act_id = $Activity->act_id;
//$Activity = Activity::find_by_actid($act_id);
$brand_activity = BrandActivity::find_by_act_id($act_id);



if (isset($_POST['edit'])) {
    $newActivity = new Activity();
    $newActivity->act_id = $_GET['act_id'];
    if ($_POST['activity_type'] == '12') {
        $newActivity->activity_type = $_POST['activity_type1'];
    } else {
        $newActivity->activity_type = $_POST['activity_type'];
    }

    if (!empty($_POST['activity_date'])) {
        $newActivity->activity_date = $_POST['activity_date'];
    } else {
        array_push($errors2, "Please Enter Activity Date.");
    }

    if (!empty($_POST['doctor_name'])) {
        $newActivity->doc_id = $_POST['doctor_name'];
    } else {
        array_push($errors2, "Please Enter Doctor Name");
    }

    if (!empty($_POST['expances'])) {
        $newActivity->expances = $_POST['expances'];
    } else {
        array_push($errors2, "Please Enter Expances Details");
    }

    $newActivity->brand1 = $_POST['brand1'];
    $newActivity->brand2 = $_POST['brand2'];
    $newActivity->brand3 = $_POST['brand3'];
    $newActivity->brand4 = $_POST['brand4'];
    $newActivity->brand5 = $_POST['brand5'];
    $newActivity->brand6 = $_POST['brand6'];
    $newActivity->brand7 = $_POST['brand7'];
    $newActivity->brand8 = $_POST['brand8'];
    $newActivity->total = $_POST['total'];
    $newActivity->highlight = $_POST['highlight'];
    $newActivity->empid = $_SESSION['employee'];
    $newActivity->filename = $_POST['filename'];
    $newActivity->update($newActivity->act_id);

    /*     * ************************ edit ******************************* */
    $BrandActivity = new BrandActivity();

    for ($i = 1; $i <= 8; $i++) {
        $BrandActivity->{'brand' . $i} = $_POST['brandlist' . $i];
    }
    $BrandActivity->act_id = $newActivity->act_id;
    $BrandActivity->id = $_POST['id'];
    $BrandActivity->update();
    redirect_to('viewActivityDetails.php?act_id=' . $BrandActivity->act_id);
}
require_once("layouts/TMheader.php");
?>
<script src="js/lightbox.js" type="text/javascript"></script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?php
            $ActName = ActivityMaster::find_by_id($Activity->activity_type);
            echo isset($ActName->activity) ? $ActName->activity : $Activity->activity_type;
            ?></h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class ="row">
    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            if ($pagination->has_previous_page()) {
                echo "<div class='col-lg-1'> <a class ='btn btn-default' href=\"viewActivityDetails.php?page=";
                echo $pagination->previous_page();
                echo "\">&laquo; Previous</a></div> ";
            }

            echo '<div class="col-lg-10" style = "text-align:center">';
            for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                if ($i == $page) {
                    echo " <span class=\"selected\"  style='color:red;padding:1px;font-weight:bold;border:1px solid aqua;radius:1px;'>{$i}</span> ";
                } else {
                    echo " <a href=\"viewActivityDetails.php?page={$i}\">{$i}</a> ";
                }
            }
            echo '</div>';

            if ($pagination->has_next_page()) {
                echo "<div class ='col-lg-1 pull-right'> <a class ='btn btn-default' href=\"viewActivityDetails.php?page=";
                echo $pagination->next_page();
                echo "\">Next &raquo;</a> </div>";
            }
        }
        ?>
    </div>
</div>
<div class="row row-margin-top">
    <div class="col-lg-1 pull-right">
        <input type="button" id="<?php echo $act_id; ?>" onclick="editActivity(this.id)" class="btn btn-primary" value="Edit">
    </div>
</div>

<div class="row row-margin-top">
    <div class="col-lg-6">
        <dl class="dl-horizontal">
            <dt>Activity Date</dt>
            <?php echo '<dd>' . date('d-m-Y ', strtotime($Activity->activity_date)) . '</dd>'; ?>
        </dl>
    </div>
    <div class="col-lg-6">
        <dl class="dl-horizontal">
            <dt>Doctor Name</dt>
            <?php
            $doctor_name = Doctor::find_by_docid($Activity->doc_id);
            echo '<dd>' . $doctor_name->name . '</dd>';
            ?>
        </dl>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <label>Rx Grand Total</label>
        <hr>
        <table class="table table-bordered table-hover " id="items" >
            <tr>
                <?php for ($i = 1; $i <= 8; $i++) { ?>

                    <?php
                    $Product = Product::find_by_id($brand_activity->{'brand' . $i});
                    if (isset($Product->name))
                        echo "<th>" . $Product->name . "</th>"
                        ?>


                <?php } ?>

                <th>Total Business</th>
            </tr>
            <tr class="targetfields">
                <?php
                $finalTotal = 0;
                for ($i = 1; $i < 9; $i++) {
                    $Product = Product::find_by_id($brand_activity->{'brand' . $i});
                    if (isset($Product->name)) {
                        ?>
                        <td>
                            <?php
                            echo $Activity->{'brand' . $i};
                            $finalTotal += $Activity->{'brand' . $i};
                            ?>
                        </td>
                        <?php
                    }
                }

                echo '<td>' . $Activity->total . '</td>';
                ?>

            </tr>
        </table>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12">
        <label>Highlights Of Activity</label>
        <hr>
        <p><?php echo $Activity->highlight; ?></p>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12"><label>Attachments</label>
        <hr></div>

    <?php
    $filesnames = explode(",", $Activity->filename);
    $filesnames = array_filter(array_map('trim', $filesnames));
    if (!empty($filesnames)) {
        foreach ($filesnames as $file) {
            $path1 = 'activities/' . $file;
            $filename = isset($file) && $file != '' && file_exists($path1) ? $path1 : '';
            $filename = $GLOBALS['site_root'] . '/' . $filename;
            if ($filename != '') {
                ?>
                <div class="col-xs-6 col-sm-3">
                    <a href="#" class="thumbnail" data-toggle="modal" data-target="#lightbox"> 
                        <img src="<?php echo $filename; ?>" alt="Image Not Found">
                    </a>
                </div>
                <?php
            }
        }
    }
    ?>

</div>
<div id="lightbox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <button type="button" class="close hidden" data-dismiss="modal" aria-hidden="true">×</button>
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="" />
            </div>
        </div>
    </div>
</div>
<div id="modalpopup"></div>
<script>
    function editActivity(id) {
        var callid = id;
        $.ajax({
            //Send request
            type: 'GET',
            data: {act_id: callid},
            url: 'editActivity.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    }
</script>
<?php require_once("layouts/TMfooter.php"); ?>
