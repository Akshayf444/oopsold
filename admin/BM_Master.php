<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");


if (isset($_POST['submit'])) {
    $bm = new BM();
    $bm->bm_empid = $_POST['bm_empid'];
    $bm->name = $_POST['name'];
    $bm->emailid = $_POST['emailid'];
    $bm->mobile = $_POST['mobile'];
    $bm->sm_empid = $_POST['sm_empid'];
    $bm->password = 'cipla@' . $bm->bm_empid;
    $entryExist = User::sm_bm_tm_exist($bm->bm_empid);
    if ($entryExist === FALSE) {
        $bm->create();
        flashMessage('Record Added Successfully.', 'success');
    } else {
        flashMessage('Entry Exist For This Employee Id', 'error');
    }
}

$BM = BM::find();
require_once("adminheader.php");
echo pageHeading('BM List');
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<link href="../css/dataTables.bootstrap.css" rel="stylesheet" type="text/css"/>
<script src="../js/jquery.dataTables.min.js" type="text/javascript"></script>
<div class="row">
    <div class="col-lg-12 ">
        <button class="btn btn-success" onclick="sendRequest('add')"><i class="fa fa-plus-circle"></i> Add New</button>
    </div>
</div>
<div class="row row-margin-top" >
    <div class="col-lg-12 col-md-12 col-sm-12  col-xs-12 table-responsive">
        <table class="table table-bordered" id="bmlist">
            <thead>
                <tr>
                    <th>BM-Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
<!--                    <th>Action</th>-->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($BM as $item) { ?>
                    <tr>
                        <td><?php echo $item->bm_empid ?></td>
                        <td><?php echo $item->name ?></td>
                        <td><?php echo $item->emailid ?></td>
                        <td><?php echo $item->mobile ?></td>
<!--                        <td>
                            <button class="btn btn-info btn-xs " id="<?php echo $item->bm_empid ?>" onclick="sendRequest(this.id)"><i class="fa fa-edit"></i></button>
                        </td>-->
                    </tr>
                <?php } ?>
            </tbody>
        </table>
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
            data: {id: id, type: 'bm', action: action},
            url: 'action.php',
            success: function (data) {
                $("#modalpopup").html(data);
            }
        });
    }


    $(document).ready(function () {
        $('#bmlist').dataTable({
            "bPaginate": false,
            "bInfo": false
        });
    });
</script>
<?php require_once("adminfooter.php");
?>