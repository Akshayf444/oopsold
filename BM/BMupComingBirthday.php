<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$bm_empid = $_SESSION['BM'];
$employees = Employee::find_by_bmid($bm_empid);

$pageTitle = "Upcoming Birthdays";
require_once("BMheader.php");
?>

<script>
    function Search() {

        var search_term = $(".employee").val();
        $.post('getWeekWiseBirthdayList.php', {search_term: search_term}, function (data) {

            $('.result').css("background", "#fff");
            $('.table-responsive').html(data);
            $('.table-responsive .employee').remove();
            $('.table-responsive .employee2').remove();
            oTable = $('#searchtable').dataTable({
                "bPaginate": false,
                "bInfo": false
            });

        });
    }

</script>
<script>
    function Search2() {

        var search_term1 = $(".employee2").val();
        $.post('getWeekWiseBirthdayList.php', {search_term1: search_term1}, function (data) {
            $('.result').css("background", "#fff");

            $('.table-responsive').html(data);
            $('.table-responsive .employee2').remove();
            $('.table-responsive .employee').remove();
            oTable = $('#searchtable').dataTable({
                "bPaginate": false,
                "bInfo": false
            });
        });
    }

</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Upcoming Birthdays</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">
        <select  onchange="Search()" class="employee form-control">
            <option value="">Select Birthdays</option>
            <option value="Week">Week</option>
            <option value="Month">Month</option>
            <option value="3 Months">Next 3 Months</option>
        </select>
    </div>

    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">
        <select  onchange="Search2()" class="employee2 form-control">
            <option value="select">Select Employee</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?php echo $employee->name; ?>"><?php echo $employee->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="row" style="margin-top:1em;">
    <div class="result col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive" id="no-more-tables">

        </div>
    </div>
</div>
<?php require_once("BMfooter.php"); ?>