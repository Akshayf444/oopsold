<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$pageTitle = "Birthdays";
$empName = Employee::find_by_empid($_SESSION['employee']);
?>
<?php require_once("layouts/TMheader.php"); ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Upcoming Birthdays</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">
        <form action="upComingBirthday.php" method="post">
            <select name="search_term" onchange="this.form.submit()" class="form-control">
                <option value="">Select Birthdays</option>
                <option value="Week" <?php
                if (isset($_POST['search_term']) && $_POST['search_term'] == 'Week') {
                    echo 'selected';
                }
                ?>>Week</option>
                <option value="Month" <?php
                if (isset($_POST['search_term']) && $_POST['search_term'] == 'Month') {
                    echo 'selected';
                }
                ?>>Month</option>
                <option value="3 Months" <?php
                if (isset($_POST['search_term']) && $_POST['search_term'] == '3 Months') {
                    echo 'selected';
                }
                ?>>Next 3 Months</option>
            </select>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 ">
        <div class="table-responsive" style="margin-top:2em">
            <?php
            if (isset($_POST['search_term']) && ($_POST['search_term'] == 'Week')) {
                $result = BasicProfile::NextWeekBirthdays($_SESSION['employee']);
                if (!empty($result)) {
                    echo "<table class= 'table table-bordered table-hover '>
                            <tr>
                            <th>Name</th>
                            <th style='width:20%'>Mobile</th>
                            <th style='width:20%'>Area</th>
                            <th style='width:20%'>Date Of Birth</th>
                            </tr>";

                    foreach ($result as $doctor) {

                        $DoctorDetails = Doctor::find_by_docid($doctor->docid);
                        echo "<tr>";
                        echo'<td>' . $DoctorDetails->name . '</a></td>';
                        echo '<td>' . $DoctorDetails->mobile . '</td>';
                        echo '<td>' . $DoctorDetails->area . '</td>';

                        echo '<td>' . date('d-m-Y', strtotime($doctor->DOB)) . '</td>';
                        echo "</tr>";
                    }
                } else {
                    echo "Dont have Birthday In next week";
                }
            }


            if (isset($_POST['search_term']) && ($_POST['search_term'] == 'Month')) {
                $result = BasicProfile::NextMonthBirthdays($_SESSION['employee']);
                if (!empty($result)) {
                    echo "<table  class= 'table table-bordered table-hover ' >
                            <tr>
                            <th>Name</th>
                            <th style='width:20%'>Mobile</th>
                            <th style='width:20%'>Area</th>
                            <th style='width:20%'>Date Of Birth</th>
                            </tr>";

                    foreach ($result as $doctor) {
                        $DoctorDetails = Doctor::find_by_docid($doctor->docid);
                        echo "<tr>";
                        echo'<td>' . $DoctorDetails->name . '</a></td>';
                        echo '<td>' . $DoctorDetails->mobile . '</td>';
                        echo '<td>' . $DoctorDetails->area . '</td>';
                        $date = BasicProfile::findBirthDate($doctor->docid);
                        echo '<td>' . date('d-m-Y', strtotime($date->DOB)) . '</td>';
                        echo "</tr>";
                    }
                } else {
                    echo "Dont have Birthday In next month";
                }
            }


            if (isset($_POST['search_term']) && ($_POST['search_term'] == '3 Months')) {
                $result = BasicProfile::Next3MonthBirthdays($_SESSION['employee']);
                if (!empty($result)) {
                    echo "<table  class= 'table table-bordered table-hover '>
	<tr>
	<th>Name</th>
	<th style='width:20%'>Mobile</th>
	<th style='width:20%'>Area</th>
	<th style='width:20%'>Date Of Birth</th>
	</tr>";

                    foreach ($result as $doctor) {
                        $DoctorDetails = Doctor::find_by_docid($doctor->docid);
                        echo "<tr>";
                        echo'<td>' . $DoctorDetails->name . '</a></td>';
                        echo '<td>' . $DoctorDetails->mobile . '</td>';
                        echo '<td>' . $DoctorDetails->area . '</td>';
                        $date = BasicProfile::findBirthDate($doctor->docid);
                        echo '<td>' . date('d-m-Y', strtotime($date->DOB)) . '</td>';
                        echo "</tr>";
                    }
                } else {
                    echo "Dont have Birthday In next 3 months";
                }
            }
            ?>
        </div>
    </div>
</div>
<script>
    function Search() {
        $(".result").css("background", " url('images/loader.gif') no-repeat scroll center center ");
        var search_term = $(".employee").val();
        $.post('upComingBirthday.php', {search_term: search_term}, function (data) {
            $('.result').css("background", "#fff");
            $('.result').html(data);
            $('.result .employee').remove();
        });
    }
</script>

<?php require_once("layouts/TMfooter.php"); ?>