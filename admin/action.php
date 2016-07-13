<?php
require_once("../includes/initialize.php");
$output = '';
$colspan = 0;
$header = '';
$smList = SM::find();
$bmList = BM::find();
$Zones = Employee::find_zone();
$width = '';
$list = '<option>Select SM</option>';
$list2 = '<option>Select BM</option>';
$list3 = '<option>Select Zone</option>';
foreach ($smList as $sm) {
    $list .='<option value="' . $sm->sm_empid . '">' . $sm->name . '</option>';
}
foreach ($bmList as $bm) {
    $list2 .='<option value="' . $bm->bm_empid . '">' . $bm->name . '</option>';
}
foreach ($Zones as $zone) {
    $list3.='<option value="' . $zone->Zone . '">' . $zone->Zone . '</option>';
}
$team = '<option>Select Team</option><option value="1">Team 1</option><option value="2">Team 2</option><option value="3">Team COMMON</option>';

if (isset($_POST['id'])) {
    $empid = $_POST['id'];
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        if ($_POST['type'] == 'TM') {
            $found_tm = Employee::find_by_empid($empid);
        } elseif ($_POST['type'] == 'BM') {
            $found_bm = BM::find_by_bmid($empid);
        } elseif ($_POST['type'] == 'SM') {
            $found_sm = SM::find_by_smid($empid);
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        if ($_POST['type'] == 'tm') {
            $header = '<i class="fa fa-plus-circle"></i> Add TM';
            $colspan = 10;
            $width = '90%';
            $output = '<table class="table table-bordered ">
                        <thead>
                            <tr> <th > TM Id </th> <th >Name</th><th>BM</th>
                            <th>Email</th><th>Mobile</th><th>HQ</th><th>State</th>
                            <th>Region</th><th>Team</th><th>Zone</th></tr>                            
                        </thead>
                        <tbody >';
            $output .='</tbody>
                        <tr>
                            <td><input type="text" name="empid" class="form-control" required ></td>                           
                            <td><input type="text" name="name" class="form-control" required></td>
                            <td><select name="bm_empid" class="form-control" >' . $list2 . '</select></td>
                            <td><input type="text" name="emailid" class="form-control" required></td>
                            <td><input type="text" name="mobile" class="form-control" required></td>
                            <td><input type="text" name="HQ" class="form-control" required></td>
                            <td><input type="text" name="state" class="form-control" required></td>
                            <td><input type="text" name="region" class="form-control" required></td>
                            <td><select name="team" class="form-control" >' . $team . '</select></td>
                            <td><select name="zone" class="form-control" >' . $list3 . '</select></td>
                           <tr>';
        } elseif ($_POST['type'] == 'bm') {

            $header = '<i class="fa fa-plus-circle"></i> Add BM';
            $colspan = 5;
            $output .= '<table class="table table-bordered ">
                        <thead>
                            <tr> <th > BM Id </th> <th >   Name  </th><th>Email</th><th>Mobile</th><th>SM</th></tr>                            
                        </thead>
                        <tbody >
                        <tr>
                            <td><input type="text" name="bm_empid" class="form-control" required ></td>                           
                            <td><input type="text" name="name" class="form-control" required></td>
                            <td><input type="text" name="emailid" class="form-control" required></td>
                            <td><input type="text" name="mobile" class="form-control" required></td>
                            <td><select name="sm_empid" class="form-control" >' . $list . '</select></td>
                        </tr>';
        } elseif ($_POST['type'] == 'sm') {
            $header = '<i class="fa fa-plus-circle"></i> Add SM';
            $colspan = 4;
            $output .= '<table class="table table-bordered ">
                        <thead>
                            <tr> <th > SM Id </th> <th >   Name  </th><th>Email</th><th>Mobile</th></tr>                            
                        </thead>
                        <tbody >
                        <tr>
                            <td><input type="text" name="sm_empid" class="form-control" required ></td>
                            <td><input type="text" name="name" class="form-control" required></td>
                            <td><input type="text" name="emailid" class="form-control" required></td>
                            <td><input type="text" name="mobile" class="form-control" required></td>
                        </tr>';
        }
    }
}
?>
<div id="fullCalModal" class="modal ">
    <div class="modal-dialog modal-lg" style="width:<?php echo $width ?>">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 ><?php echo $header; ?></h4>
            </div>
            <form action="" method="post">
                <div  class="modal-body">
                    <?php
                    if (isset($output)) {
                        echo $output;
                    }
                    ?>
                    <tr>
                        <td colspan ='<?php echo $colspan ?>'>
                            <input type="submit" class="btn btn-primary" value="Save" name="submit">
                        </td>
                    </tr>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var isIE = navigator.userAgent.indexOf(' MSIE ') > -1;
        if (isIE) {
            $('#BookAppointment').removeClass('fade');
        }
        $("#fullCalModal").modal();
    });
</script>