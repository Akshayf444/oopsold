<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$Zones = Employee::find_zone();

require_once("adminheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Areawise Report</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row" style="margin-top:1em;">
    <div id="result"  class="table-responsive col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <table class="table table-bordered table-hover ">
            <tr>
                <th>Zone</th>
                <th>No of Doctors</th>
                <th>No of Completed Profiles</th>
                <th>Completed Basic Profiles</th>
                <th>Completed Academic Profiles</th>
                <th>Completed Service Profiles</th>
            </tr>

            <?php
            foreach ($Zones as $Zone) {
                $report = User::zoneWiseReport($Zone->Zone);
                $completed_profile = User::zonewiseCompletedProfiles($Zone->Zone);
                echo "<tr><td>" . $Zone->Zone . "</td><td>" . $report->doctor_count . "</td><td>" . $completed_profile->doctor_count . "</td><td>" . $report->basic_count . "</td><td>" . $report->academic_count . "</td><td>" . $report->service_count . "</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
<?php
require_once("./adminfooter.php");
