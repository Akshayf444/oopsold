<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");

$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$total_count = Employee::count();
$per_page = 100;
$pagination = new Pagination($page, $per_page, $total_count);
$Employee = Employee::pagination($per_page, $pagination->offset());

if (isset($_POST['submit'])) {
    $newEmployee = new Employee();
    $newEmployee->HQ = $_POST['HQ'];
    $newEmployee->name = $_POST['name'];
    $newEmployee->cipla_empid = $_POST['empid'];
    $newEmployee->bm_empid = $_POST['bm_empid'];
    $newEmployee->emailid = $_POST['emailid'];
    $newEmployee->mobile = $_POST['mobile'];
    $newEmployee->password = 'cipla@' . $newEmployee->cipla_empid;
    $newEmployee->state = $_POST['state'];
    $newEmployee->city = $_POST['HQ'];
    $newEmployee->zone = $_POST['zone'];
    $newEmployee->region = $_POST['region'];
    $newEmployee->team = $_POST['team'];
    $newEmployee->create();
    redirect_to('TM_Master.php?page='.$page);
}
require_once("adminheader.php");
echo pageHeading('TM List');
?>
<link href="../css/dataTables.bootstrap.css" rel="stylesheet" type="text/css"/>
<script src="../js/jquery.dataTables.min.js" type="text/javascript"></script>
<!--<script src="../js/excellentexport.min.js" type="text/javascript"></script>-->
<div class="row">
    <div class="col-lg-12 ">
        <button class="btn btn-success"  onclick="sendRequest('add')"><i class="fa fa-plus-circle"></i> Add New</button>
        <!--        <a download="somedata.csv" href="#" onclick="return ExcellentExport.csv(this, 'tmlist');">Export to CSV</a>-->
    </div>
</div>
<div class="row row-margin-top" >
    <div class="col-lg-12 col-md-12 col-sm-12  col-xs-12 table-responsive">
        <table class="table table-bordered" id="tmlist">
            <thead>
                <tr>
                    <th>TM-Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
<!--                    <th>Action</th>-->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Employee as $item) { ?>
                    <tr>
                        <td><?php echo $item->cipla_empid ?></td>
                        <td><?php echo $item->name ?></td>
                        <td><?php echo $item->emailid ?></td>
                        <td><?php echo $item->mobile ?></td>
    <!--                        <td>
                            <button class="btn btn-info btn-xs tm" id="<?php echo $item->empid ?>" onclick="sendRequest(this.id)"><i class="fa fa-edit"></i></button>
                        </td>-->
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div class ="row">
    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            echo '<div class="col-lg-10" style = "text-align:center">';
            for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                ?>
                <a class="btn btn-xs btn-primary" href="TM_Master.php?page=<?php echo $i; ?>" <?php if ($page == $i) {
            echo 'style="border:1px solid red"';
        } ?> ><?php echo $i; ?></a>
                <?php
            }
            echo '</div>';
        }
        ?>
    </div>
</div>
<div id="modalpopup"></div>
<script>
    function sendRequest(id) {
        var id = id;
        var action = 'edit';
        if (id == 'add') {
            action = 'add';
        }
        $.ajax({
            type: 'POST',
            data: {id: id, type: 'tm', action: action},
            url: 'action.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    }

    $(document).ready(function () {
        $('#tmlist').dataTable({
            "bPaginate": false,
            "bInfo": false
        });
    });
</script>
<?php require_once("adminfooter.php"); ?>