<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$empid = $_SESSION['employee'];
$empname = Employee::find_by_empid($empid);
$pageTitle = "Add Business Competitors";
$empName = Employee::find_by_empid($_SESSION['employee']);

$doctors = Doctor::find_all($empid);
$totalDoctors = Doctor::count_all($empid);


$i = 0;
$postCount = null;
//collecting errors
$errors = array();
$errors2 = array();

if (isset($_POST['submit'])) {

    for ($i = 0; $i < $totalDoctors; $i++) {
        $newCompetitors = new Competitors(); //Create Object of class

        $newCompetitors->docid = $_POST["docid"][$i];
        $newCompetitors->empid = $empid;
//        $newCompetitors->cipla = trim($_POST['cipla'][$i]);
        $newCompetitors->company1 = trim($_POST['company1'][$i]);
        $newCompetitors->company2 = trim($_POST['company2'][$i]);
        $newCompetitors->company3 = trim($_POST['company3'][$i]);
        $newCompetitors->company4 = trim($_POST['company4'][$i]);
        $newCompetitors->company5 = trim($_POST['company5'][$i]);
        $newCompetitors->company6 = trim($_POST['company6'][$i]);
        $newCompetitors->company7 = trim($_POST['company7'][$i]);
        $newCompetitors->name = trim($_POST['name'][$i]);

        if (empty($errors2)) {
            $business = Competitors::find_by_docid($newCompetitors->docid);
            if (!empty($business)) {
                $newCompetitors->update($newCompetitors->docid);
            } else {
                if ($newCompetitors->cipla != '' || $newCompetitors->company1 != '' || $newCompetitors->company2 != '' || $newCompetitors->company3 != '' || $newCompetitors->company4 != '' || $newCompetitors->company5 != '' || $newCompetitors->company6 != '' || $newCompetitors->company7 != '') {
                    $newCompetitors->create();
                }
            }
        }
    }
    if (empty($errors2)) {
        flashMessage("Record Added Successfully.", 'Success');
        redirect_to("AddCompetitor.php");
    }
}
?>
<?php require_once("layouts/TMheader.php"); ?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Doctor wise Competitor's Business (in Rs.)</h1>
    </div>      <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>
        <ul>
            <?php foreach (array_unique($errors2) as $val) { ?>
                <li style="color:red"><?php echo $val; ?></li>
            <?php } ?>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div class="" id="no-more-tables">
            <form action="AddCompetitor.php" method="post" id="multipleForm">
                <table class="table table-bordered table-hover " id="searchtable">
                    <thead>
                        <tr>
                            <th style="width:10%">Doctor Name</th>
                            <th>Area</th>
                            <th>ALLERGAN</th>
                            <th>SUN</th>
                            <th>ALCON</th>
                            <th>AJANTA</th>
                            <th>MICRO LAB</th>
                            <th>FDC</th>
                            <th>INTAS</th>
    <!--                        <th>CIPLA</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $company = array('', 'ALLERGAN', 'SUN', 'ALCON', 'AJANTA', 'MICRO LAB', 'FDC', 'INTAS');
                        foreach ($doctors as $doctor) {
                            $business = Competitors::find_by_docid($doctor->docid);
                            ?>
                            <tr> 				
                                <td data-title="Doctor Name"><?php echo $doctor->name; ?></td>
                                <td data-title="Area"><?php
                                    echo $doctor->area;
                                    $docid = $doctor->docid;
                                    ?>
                                    <input type="hidden" name="docid[]" maxlength="50" value="<?php echo $docid; ?>" />
                                    <input type="hidden" name="name[]" maxlength="50" value="<?php echo $doctor->name; ?>" />
                                </td>
                                <?php for ($i = 1; $i < 8; $i++) { ?>
                                    <td data-title="<?php echo $company[$i]; ?>">
                                        <div class="form-group">
                                            <input type="text" name="company<?php echo $i; ?>[]" class="form-control number" maxlength="50"  value="<?php
                                            if (isset($business->{'company' . $i})) {
                                                echo $business->{'company' . $i};
                                            }
                                            ?>" placeholder="Value in Rs."/>
                                        </div>
                                    </td>
                                <?php } ?>

                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-xs-12">
            <input type="submit" name="submit" class="btn btn-primary" value="Save"  />
        </div>
    </div>
</div>
<script language="javascript" type="text/javascript">
    $('.number').change(function () {
        var mobile = /[0-9]/;
        if (!(mobile.test($(this).val()))) {
            alert('Please Enter Value In number');
        }
    });

</script>
<?php require_once("layouts/TMfooter.php"); ?>