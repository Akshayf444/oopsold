<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];
if (isset($_POST['month'])) {
    $month = $_POST['month'];
} else {
    $month = $_SESSION['_CURRENT_MONTH'];
}

$doctors = Planning::ListOfMissedDoctors($empid, $month);
$doctorWithClass = Planning::DoctorWithClass($empid);

require_once("layouts/TMheader.php");

echo pageHeading('List Of Missed Doctors');
?>
<div class="row">
    <form action="#" method="post" id="form1">
        <div class="col-lg-3 center-block"> 
            <select class="form-control" id="month" onchange="this.form.submit()" name="month">
                <option value="">SELECT MONTH</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month = $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                    if (isset($_POST['month']) && $_POST['month'] == $m) {
                        echo '<option value="' . $m . '"  selected >' . $month . '</option>';
                    } else {
                        echo '<option value="' . $m . '"   >' . $month . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </form>
</div>
<div class="row row-margin-top">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover ">
                <tr>
                    <th>Name</th>
                    <th>Area</th>
                    <th>Class</th>
                    <th>Required Count</th>
                    <th>Visit Count</th>
                </tr>
                <?php
                if (!empty($doctors)) {
                    foreach ($doctors as $doctor) {
                        $key = array_search($doctor->docid, $doctorWithClass);
                        unset($doctorWithClass[$key]);
                        ?>
                        <tr <?php
                        if ($doctor->visit_count >= $doctor->required_count) {
                            echo 'style="display:none"';
                        }
                        ?>>
                            <td><?php echo $doctor->name; ?></td>
                            <td><?php echo $doctor->area; ?></td>
                            <td><?php echo $doctor->class ?></td>
                            <td><?php echo $doctor->required_count; ?></td>
                            <td><?php echo $doctor->visit_count ?></td>

                        </tr>
                        <?php
                    }
                    if (!empty($doctorWithClass)) {
                        $NonVisit = Planning::NonVisitedDoctors(join(",", $doctorWithClass));
                        if (!empty($NonVisit)) {
                            foreach ($NonVisit as $doctor) {

                                echo '<tr>'
                                . '<td>' . $doctor->name . '</td>'
                                . '<td>' . $doctor->area . '</td>'
                                . '<td>' . $doctor->class . '</td>'
                                . '<td>0</td>'
                                . '<td>' . $doctor->required_count . '</td>'
                                . '</tr>';
                            }
                        }
                    }
                } else {
                    
                }
                ?>
            </table>
        </div>
    </div>
</div>
<?php require_once("layouts/TMfooter.php"); ?>