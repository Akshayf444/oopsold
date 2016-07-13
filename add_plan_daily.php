<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$events = Planning::find_by_empid($_SESSION['employee']);

if (isset($_POST['submit']) && isset($_POST['doctors'])) {
    $_SESSION['visit_ids'] = array();
    $trimmed_array = array_filter(array_map('trim', $_POST['doctors']));
    $newVisit = new DoctorVisit();
    foreach ($trimmed_array as $docid) {
        $newVisit->docid = $docid;
        $newVisit->empid = $_SESSION['employee'];
        $newVisit->visit_date = date('Y-m-d', time());
        $newVisit->plan_id = $_POST['plan_id'];
        $result = $newVisit->create();
        array_push($_SESSION['visit_ids'], $result);
    }
    redirect_to('view_daily_plan.php?plan_id='.$newVisit->plan_id);
}

require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Daily Planning</h1>
    </div>      <!-- /.col-lg-12 -->
</div>

<div id="calendar"  >

</div>
<div id="modalpopup"></div>
<?php require_once("layouts/TMfooter.php"); ?>
<link href="css/fullcalendar.print.css" rel="stylesheet" type="text/css" media="Print"/>
<link href="css/fullcalendar.min.css" rel="stylesheet" type="text/css" />
<script src="js/moment.min.js" type="text/javascript"></script>
<script src="js/fullcalendar.min.js" type="text/javascript"></script>

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