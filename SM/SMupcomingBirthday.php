<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$sm_empid = $_SESSION['SM'];

$BMs = BM::find_all($sm_empid);
$myurl = array();
$pageTitle = "Upcoming Birthdays";
require_once("SMheader.php");
?>

<script>
    function Search() {

        var search_term = $(".employee").val();
        $.post('SMgetWeekWiseBirthdayList.php', {search_term: search_term}, function (data) {
            $('.result').css("background", "#fff");

            $('.table-responsive').html(data);
            $('.result .employee').remove();
            $('.result .employee2').remove();
            $('.result .employee1').remove();
            oTable = $('#searchtable').dataTable({
                "bPaginate": false,
                "bInfo": false
            });
        });
    }

</script>
<script>
    function Search2() {

        var search_term1 = $(".employee1").val();
        $.post('SMgetWeekWiseBirthdayList.php', {search_term1: search_term1}, function (data) {

            $('.result').css("background", "#fff");
            $('.table-responsive').html(data);
            $('.result .employee2').remove();
            $('.result .employee').remove();
            $('.result .employee1').remove();
            oTable = $('#searchtable').dataTable({
                "bPaginate": false,
                "bInfo": false
            });
        });
    }

</script>
<script>
    function Search3() {

        var search_term2 = $(".employee2").val();
        $.post('getData.php', {search_term2: search_term2}, function (data) {

            $('.result').css("background", "#fff");
            $('#employees').html(data);
            $('.result .employee2').remove();
            $('.result .employee').remove();
            $('.result .employee1').remove();
        });
    }

</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Upcoming Birthday</h1>
    </div>      <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8 ">
        <select  onchange="Search()" class="employee form-control">
            <option value="">Select Birthdays</option>
            <option value="Week">Week</option>
            <option value="Month">Month</option>
            <option value="3 Months">Next 3 Months</option>
        </select>
    </div>
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8 ">
        <select  onchange="Search3()" class="employee2 form-control">
            <option value="select">Select BM</option>
            <?php foreach ($BMs as $BM): ?>
                <option value="<?php echo $BM->name; ?>"><?php echo $BM->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="employees" class="col-lg-3 col-sm-4 col-md-4 col-xs-8 ">
    </div>
</div>

<div class="row" style="margin-top:1em;">
    <div class="result col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive" id="no-more-tables">
        </div>
    </div>
</div>
<?php require_once("SMfooter.php"); ?>