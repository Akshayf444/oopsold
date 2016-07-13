<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:logout.php");
}
require_once("../includes/initialize.php");
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 1;
if (isset($_GET['docid'])) {
    $docid = $_GET['docid'];
    $sql = "SELECT COUNT(*) FROM doctors WHERE docid = '$docid'
                UNION ALL
            SELECT COUNT(*) FROM doctors WHERE docid <> '$docid' ";
    $total_count = Doctor::returnCount($sql);
    $pagination = new Pagination($page, $per_page, $total_count);

    $sql = "SELECT * FROM doctors WHERE docid = '$docid'
                UNION ALL
            SELECT * FROM doctors WHERE docid <> '$docid'  LIMIT {$per_page} OFFSET {$pagination->offset()} ";
} else {
    $total_count = Doctor::all();
    $pagination = new Pagination($page, $per_page, $total_count);
    $sql = "SELECT * FROM doctors WHERE is_delete = 0  ";
    $sql .= " LIMIT {$per_page} ";
    $sql .= " OFFSET {$pagination->offset()}";
}

$doctor = Doctor::find_by_sql($sql);
$doctor = !empty($doctor) ? array_shift($doctor) : FALSE;
$docid = $doctor->docid;
$totalProfileCompleted = Doctor::profile_complete_percentage($docid);

$url = 'viewFilteredProfiles.php';
include 'adminheader.php';
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo ucfirst($doctor->name); ?></h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class ="row">
    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            if ($pagination->has_previous_page()) {
                echo "<div class='col-lg-1'> <a class ='btn btn-default' href=\"{$url}?page=";
                echo $pagination->previous_page();
                echo "\">&laquo; Previous</a></div> ";
            }

            echo '<div class="col-lg-10" style = "text-align:center">';
            if (isset($_SESSION['BM']) && isset($_SESSION['employee'])) {
                for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                    if ($i == $page) {
                        echo " <span class=\"selected\"  style='color:red;padding:1px;font-weight:bold;border:1px solid aqua;radius:1px;'>{$i}</span> ";
                    } else {
                        echo " <a href=\"{$url}?page={$i}\">{$i}</a> ";
                    }
                }
            }

            echo '</div>';

            if ($pagination->has_next_page()) {
                echo "<div class ='col-lg-1 pull-right'> <a class ='btn btn-default' href=\"{$url}?page=";
                echo $pagination->next_page();
                echo "\">Next &raquo;</a> </div>";
            }
        }
        ?>
    </div>
</div>
<div class ="row row-margin-top">
    <div class="col-lg-3 panel-default">
        <div class=" panel-default">

            <div class=" panel-body" style="background: #DDD8D8">

                <h4 >Speciality</h4>
                <hr>

                <?php echo $doctor->speciality; ?>

                <h4 class="row-margin-top" >Area</h4>
                <hr >
                <i class="fa fa-map-marker"> </i><?php echo " " . $doctor->area; ?>


                <h4 class="row-margin-top">Profile Completed</h4>
                <hr>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo ($totalProfileCompleted ); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($totalProfileCompleted ) . "%"; ?>;">
                        <?php echo ($totalProfileCompleted ) . "%"; ?>
                    </div>
                </div>

                <h4 class="row-margin-top">Contact</h4>
                <hr>
                <div class="result">
                    <i class="fa fa-mobile"> </i><span id="<?php echo $docid; ?>" onclick="editMobile(this.id)"><?php echo " " . $doctor->mobile; ?> </span>
                    <input type="text" class="form-control" id="mobileArea1" value="<?php echo $doctor->mobile; ?>" style="display:none;width: 80%">
                    <input type="button" class="btn btn-info btn-xs" id="mobileArea2" value="Edit" style="display: none" ><br>
                </div>

                <?php echo $doctor->emailid; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-sm-9 col-md-9 col-xs-9 panel-body">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#home-pills" data-toggle="tab">Basic Profile</a>
            </li>
            <li><a href="#profile-pills" data-toggle="tab">Academic Profile</a>
            </li>
            <li><a href="#messages-pills" data-toggle="tab">Services</a>
            </li>
            <li><a href="#settings-pills" data-toggle="tab">Monthwise Business Details</a>
            </li>
            <li><a href="#comp-pills" data-toggle="tab">Business Profile</a>
            </li>
        </ul>
        <div class="tab-content">
            <?php
            $basicProfile = BasicProfile::find_by_docid($docid);

            if (!empty($basicProfile)) {                ?>

                <div class="tab-pane fade in active" id="home-pills" style="margin-top: 1em">
                    <?php echo BasicProfile::view_basic_profile($basicProfile); ?>
                </div>

                <?php
            }
            $academicProfile = AcaProfile::find_by_docid($docid);
            if (!empty($academicProfile)) {        ?>
                <div class="tab-pane fade" id="profile-pills" style="margin-top: 1em">
                    <?php echo AcaProfile::viewProfile($academicProfile); ?>
                </div>
                <?php
            }
            $service = Services::find_by_docid($docid);
            if (!empty($service)) {
                ?>
                <div class="tab-pane fade" id="messages-pills" style="margin-top: 1em">
                    <?php echo Services::viewProfile($service); ?>
                </div>
                <?php }
            ?>
            <div class="tab-pane fade" id="settings-pills" style="margin-top: 1em">
                <div class="row " style="overflow-x: auto">
                    <div class="col-lg-12 col-md-12 " >
                        <?php echo BusiProfile::viewProfile($docid); ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="comp-pills" style="margin-top: 1em">
                <div class="row" style="margin-top: 1em">
                    <div class="col-lg-12 col-md-12" >
                        <?php echo Competitors::viewProfile($docid); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'adminfooter.php'; ?>