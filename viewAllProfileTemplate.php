<?php
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 1;
$total_count = 0;
$url = '';
if (isset($_SESSION['BM'])) {
    $total_count = BM::count_all_doctors($_SESSION['BM']);
    $url = 'BMviewAllProfiles.php';
} elseif (isset($_SESSION['SM'])) {
    $total_count = SM::count_all_doctors($_SESSION['SM']);
    $url = 'SMviewAllProfiles.php';
} elseif (isset($_SESSION['admin'])) {
    $total_count = Doctor::all();
    $url = 'viewAllProfiles.php';
} elseif (isset($_SESSION['zone_id'])) {
    $total_count = zone::count_all_doctors($zone_id);
    $url = 'viewAllProfiles.php';
} else {
    $total_count = Doctor::count_all($empid);
    $url = 'viewAllProfiles.php';
}
$pagination = new Pagination($page, $per_page, $total_count);

if (isset($_SESSION['BM'])) {
    $sql = "SELECT * FROM doctors WHERE empid IN ( "
            . " SELECT empid FROM employees WHERE bm_empid = '{$_SESSION['BM']}' ) AND is_delete = 0 "
            . " LIMIT {$per_page} OFFSET {$pagination->offset()} ";
} elseif (isset($_SESSION['SM'])) {
    $sql = "SELECT * FROM doctors WHERE empid IN ( "
            . " SELECT empid FROM employees WHERE bm_empid IN ( "
            . " SELECT bm_empid FROM bm WHERE sm_empid = '{$_SESSION['SM']}' ) AND is_delete = 0 "
            . ") LIMIT {$per_page} OFFSET {$pagination->offset()} ";
} elseif (isset($_SESSION['admin'])) {
    $sql = "SELECT * FROM doctors WHERE is_delete = 0  ";
    $sql .= " LIMIT {$per_page} ";
    $sql .= " OFFSET {$pagination->offset()}";
} elseif (isset($_SESSION['zone_id'])) {
    $zone_id = $_SESSION['zone_id'];
    $sql = "SELECT * FROM doctors WHERE empid IN ( "
            . " SELECT empid FROM employees WHERE zonal_id='$zone_id' "
            . " LIMIT {$per_page} OFFSET {$pagination->offset()} ";
} else {
    $sql = "SELECT * FROM doctors WHERE empid = '$empid' AND is_delete = 0  ";
    $sql .= " LIMIT {$per_page} ";
    $sql .= " OFFSET {$pagination->offset()}";
}

$doctor = Doctor::find_by_sql($sql);
$doctor = !empty($doctor) ? array_shift($doctor) : FALSE;
$docid = $doctor->docid;
$totalProfileCompleted = Doctor::profile_complete_percentage($docid);
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
                echo "<div class='col-lg-1 col-xs-1'> <a class ='btn btn-default' href=\"{$url}?page=";
                echo $pagination->previous_page();
                echo "\">&laquo; Previous</a></div> ";
            }

            echo '<div class="col-lg-10 col-xs-8" style = "text-align:center">';
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
                echo "<div class ='col-lg-1 col-xs-3 pull-right'> <a class ='btn btn-default' href=\"{$url}?page=";
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
                <div class="result3">
                    <i class="fa fa-map-marker"> </i><span ><?php echo " " . $doctor->area; ?> <button class="btn btn-xs btn-info" id="<?php echo 'area' . $docid; ?>" onclick="editarea(this.id)"><i class="fa fa-pencil"></i></button> </span>
                    <select class="form-control" id="areaArea1" style="display: none" >
                        <?php
                        $AreaList = areaList();
                        echo $AreaList;
                        ?>
                    </select>
                    <input type="button" class="btn btn-info btn-xs" id="areaArea2" value="Edit" style="display: none" ><br>
                </div>
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
                    <i class="fa fa-mobile"> </i><span ><?php echo " " . $doctor->mobile; ?> <button class="btn btn-xs btn-info" id="<?php echo $docid; ?>" onclick="editMobile(this.id)"><i class="fa fa-pencil"></i></button> </span>
                    <input type="text" class="form-control" id="mobileArea1" value="<?php echo $doctor->mobile; ?>" style="display:none;width: 80%">
                    <input type="button" class="btn btn-info btn-xs" id="mobileArea2" value="Edit" style="display: none" ><br>
                </div>
                <div class="result1">
                    <i class="fa fa-envelope"> </i><span ><?php echo " " . $doctor->emailid; ?> <button class="btn btn-xs btn-info" id="<?php echo 'email' . $docid; ?>" onclick="editEmail(this.id)"><i class="fa fa-pencil"></i></button> </span>
                    <input type="text" class="form-control" id="emailArea1" value="<?php echo $doctor->emailid; ?>" style="display:none;width: 80%">
                    <input type="button" class="btn btn-info btn-xs" id="emailArea2" value="Edit" style="display: none" ><br>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-9 col-sm-9 col-md-9 col-xs-12 panel-body">
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

            if (!empty($basicProfile)) {
                ?>

                <div class="tab-pane fade in active" id="home-pills" style="margin-top: 1em">
                    <?php if (isset($_SESSION['employee'])) { ?>
                        <div class="row">
                            <div class="col-lg-4">
                                <a href="editBasicProfile.php?docid=<?php echo $docid; ?>" class="btn btn-success">Edit</a>
                            </div>
                        </div>
                    <?php } echo BasicProfile::view_basic_profile($basicProfile); ?>
                </div>

                <?php
            } else {
                if (isset($_SESSION['employee'])) {
                    ?>
                    <div class="tab-pane fade in active" id="home-pills" style="margin-top: 1em">
                        <p>Basic Profile Dosn't exist. 
                            Add Basic Profile <a href="basicProfiles.php?docid=<?php echo $docid; ?>">Here.</a>
                        </p>
                    </div>
                    <?php
                }
            }
            $academicProfile = AcaProfile::find_by_docid($docid);
            if (!empty($academicProfile)) {
                ?>

                <div class="tab-pane fade" id="profile-pills" style="margin-top: 1em">
                    <?php if (isset($_SESSION['employee'])) { ?>
                        <div class="row">
                            <div class="col-lg-4">
                                <a href="editAcademicProfile.php?docid=<?php echo $docid; ?>" class="btn btn-success">Edit</a>
                            </div>
                        </div>
                    <?php } echo AcaProfile::viewProfile($academicProfile); ?>
                </div>

                <?php
            } else {
                if (isset($_SESSION['employee'])) {
                    ?>
                    <div class="tab-pane fade" id="profile-pills" style="margin-top: 1em">

                        <p>Academic Profile Dosn't exist. 
                            Add Basic Profile <a href="academicProfile.php?docid=<?php echo $docid; ?>">Here.</a>
                        </p>
                    </div>
                    <?php
                }
            }
            $service = Services::find_by_docid($docid);
            if (!empty($service)) {
                ?>

                <div class="tab-pane fade" id="messages-pills" style="margin-top: 1em">
                    <?php if (isset($_SESSION['employee'])) { ?>
                        <div class="row">
                            <div class="col-lg-4">
                                <a href="editService.php?docid=<?php echo $docid; ?>" class="btn btn-success">Edit</a>
                            </div>
                        </div>
                    <?php } echo Services::viewProfile($service); ?>
                </div>
                <?php
            } else {
                if (isset($_SESSION['employee'])) {
                    ?>
                    <div class="tab-pane fade" id="messages-pills" style="margin-top: 1em">
                        <p>Service Details Dosn't exist. 
                            Add Service Details <a href="service.php?docid=<?php echo $docid; ?>&action=redirect">Here.</a>
                        </p>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="tab-pane fade" id="settings-pills" style="margin-top: 1em">
                <div class="row " style="overflow-x: auto">
                    <div class="col-lg-12 col-md-12 " >
                        <?php echo BusiProfile::viewProfile($docid); ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="comp-pills" style="margin-top: 1em">
                <?php if (isset($_SESSION['employee'])) { ?>
                    <div class="row">
                        <div class="col-lg-4">
                            <a href="AddCompetitor.php" class="btn btn-success">Edit</a>
                        </div>
                    </div>
                <?php } ?>

                <div class="row" style="margin-top: 1em">
                    <div class="col-lg-12 col-md-12" id="no-more-tables" >
                        <?php echo Competitors::viewProfile($docid); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (isset($_SESSION['employee'])) { ?>
    <script>
        function editMobile(id) {
            var id = id;
            $("#" + id).hide();
            $("#mobileArea1").show();
            $("#mobileArea2").show();
            $("#mobileArea2").click(function () {
                $.ajax({
                    //Send request
                    type: 'GET',
                    data: {update_doctor_mobile: id, mobile: $("#mobileArea1").val()},
                    url: 'delete_territory.php',
                    success: function (data) {
                        $("#mobileArea1").hide();
                        $("#mobileArea2").hide();
                        $('.result').html(data);
                    }
                });
            });
        }

        function editEmail(id) {
            var id = id;
            $("#" + id).hide();
            id = id.split('email');
            id = id[1];

            $("#emailArea1").show();
            $("#emailArea2").show();
            $("#emailArea2").click(function () {
                $.ajax({
                    //Send request
                    type: 'GET',
                    data: {update_doctor_email: id, email: $("#emailArea1").val()},
                    url: 'delete_territory.php',
                    success: function (data) {

                        $("#emailArea1").hide();
                        $("#emailArea2").hide();
                        $('.result1').html(data);
                    }
                });
            });
        }

        function editarea(id) {
            var id = id;
            $("#" + id).hide();
            id = id.split('area');
            id = id[1];

            $("#areaArea1").show();
            $("#areaArea2").show();
            $("#areaArea2").click(function () {
                $.ajax({
                    //Send request
                    type: 'GET',
                    data: {update_doctor_area: id, area: $("#areaArea1").val()},
                    url: 'delete_territory.php',
                    success: function (data) {

                        $("#areaArea1").hide();
                        $("#areaArea2").hide();
                        $('.result3').html(data);
                    }
                });
            });
        }
    </script>
<?php } ?>