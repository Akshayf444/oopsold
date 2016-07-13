<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$bm_empid = $_SESSION['BM'];
$employees = Employee::find_by_bmid($bm_empid);
if (isset($_POST['empid'])) {
    $events = Planning::find_by_empid($_POST['empid']);
}
require_once("BMheader.php");
?>
<link href="../css/fullcalendar.print.css" rel="stylesheet" type="text/css" media="print"/>
<link href="../css/fullcalendar.min.css" rel="stylesheet" type="text/css"/>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Daily Planning</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <form action="" method="post">
        <div class="col-lg-4 pull-left">
            <select name="empid" class="form-control" onchange="this.form.submit()">
                <option>Select Employee</option>
                <?php foreach ($employees as $employee) { ?>
                <option value="<?php echo $employee->empid ?>" <?php if (isset($_POST['empid']) && $_POST['empid'] == $employee->empid ) echo 'selected'; ?>><?php echo $employee->name ?></option>
                <?php } ?>

            </select>
        </div>
    </form>
</div>
<div class="row row-margin-top">
    <div class="col-lg-12">
        <div id="calendar"  >

        </div>
    </div>
</div>
<div id="modalpopup"></div>
<?php require_once("BMfooter.php"); ?>
<script src="../js/moment.min.js" type="text/javascript"></script>
<script src="../js/fullcalendar.min.js" type="text/javascript"></script>
<script type='text/javascript'>
            $(document).ready(function () {
                var currentdate = new Date();
                var datetime = currentdate.getFullYear() + "-"
                        + (currentdate.getMonth() + 1) + "-"
                        + currentdate.getDate() + " "
                        + currentdate.getHours() + ":"
                        + currentdate.getMinutes() + ":"
                        + currentdate.getSeconds();
                var currentDate = moment(datetime).format('YYYY-MM-DD HH:mm:ss');

                var calendar = $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month'
                    },
                    allDaySlot: false,
                    selectable: true,
                    selectHelper: true,
                    defaultView: 'month',
                    eventClick: function (event, jsEvent, view) {

                        $.ajax({
                            //Send request
                            type: 'POST',
                            data: {id: event.id, area: event.title},
                            url: 'update_daily_plan.php',
                            success: function (data) {
                                $("#modalpopup").html(data);
                            }
                        });

                    },
                    editable: true,
                    events: <?php echo $events; ?>

                });
            });
</script>