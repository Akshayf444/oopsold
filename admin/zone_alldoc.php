<?php
session_start();
if (!isset($_SESSION['zone_id'])) {
    header("Location:/login.php");
}
require_once("../includes/initialize.php");
$bm_empid = $_SESSION['zone_id'];
$employees = zone::find_all_tm($bm_empid);
if (isset($_POST['tm_id'])) {
    $tm_id = $_POST['tm_id'];
    $allDoctors = zone::find_all_doctors($tm_id);
}
$pageTitle = "View All Doctors";

require_once("zoneheader.php");
?>
<script src="../js/ajaxLoader2.js" type="text/javascript"></script>
<script>
    function Search() {
        $('#animation').show();
        var search_term = $(".employee").val();
        $.post('BMgetAllDoctorsProfile.php', {search_term: search_term}, function (data) {
            $('#animation').hide();
            $('.table-responsive').html(data);
            $('.result .employee').remove();
        });
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">List Of Doctors</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">
        <form class="form_control" method="post" >
            <select  onchange="this.form.submit()" class="employee form-control"name="tm_id" >
                <option value="select">Select TM</option>
                <?php foreach ($employees as $employee): ?>


                    <option value="<?php echo $employee->empid; ?>" <?php if (isset($_POST['tm_id']) && $_POST['tm_id'] == $employee->empid) {
                    echo 'selected';
                } ?>><?php echo $employee->name; ?></option>

<?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<div class="row" style="margin-top:1em;">
    <div class="result col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover ">
                <tr>
                    <th style="width:25% " >Name</th>
                    <th>Speciality</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Area</th>
                    <th style="width:10% " >Profiles Completed</th>
                </tr>
                <?php
                if (!empty($allDoctors)) {
                    foreach ($allDoctors as $doctor):
                        $total = Doctor:: profile_complete_percentage($doctor->docid);
                        ?>
                        <tr <?php
                        if (isset($_GET['complete'])) {
                            if ($total != 100) {
                                echo 'style ="display : none"';
                            }
                        }
                        ?>>
                            <td><a id="<?php echo $doctor->docid ?>" class="link"><?php echo $doctor->name; ?></a></td>
                            <td><?php echo $doctor->speciality; ?></td>
                            <td><?php echo $doctor->emailid; ?></td>
                            <td><?php echo $doctor->mobile; ?></td>
                            <td><?php echo $doctor->area; ?></td>
                            <td><?php
                        echo ($total) . " %";
                        ?> 
                            </td>
                        </tr>
                        <?php
                    endforeach;
                }
                ?>
            </table>
        </div>
    </div>
</div>
<div id="modalpopup"></div>
<script>
    $('.link').click(function () {
        var id = $(this).attr('id');
        $.ajax({
            type: 'POST',
            data: {docid: id},
            url: 'zoneViewAll.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    });
</script>
<?php require_once("zonefooter.php"); ?>