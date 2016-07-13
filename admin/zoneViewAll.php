<?php require_once("../includes/initialize.php");
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 1;
$total_count = 0;
$url = '';
$docid = $_POST['docid'];

$sql = "SELECT * FROM doctors WHERE docid = '$docid' ";
$doctor = Doctor::find_by_sql($sql);
$doctor = !empty($doctor) ? array_shift($doctor) : FALSE;
$docid = $doctor->docid;
$totalProfileCompleted = Doctor::profile_complete_percentage($docid);
?>
<div id="fullCalModal" class="modal">
    <div class="modal-dialog" style="width: 1150px;height: 60%">
        <div class="modal-content">
            <div class="modal-header btn-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><?php echo ucfirst($doctor->name); ?>
                </h3>
            </div>
            <div  class="modal-body">

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
                                    <div class="col-lg-12 col-md-12" >
                                        <?php echo Competitors::viewProfile($docid); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<script>
    $(document).ready(function () {
        var isIE = navigator.userAgent.indexOf(' MSIE ') > -1;
        if (isIE) {
            $('#BookAppointment').removeClass('fade');
        }
        $("#fullCalModal").modal();
    });
</script>
