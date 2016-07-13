<?php
require_once(dirname(__FILE__) . "/includes/initialize.php");

session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
if (isset($_GET['docid'])) {
    
} else {
    redirect_to("AddAcademicProfile.php");
}

//check whether record exist or not
$academicProfile = AcaProfile::find_by_docid($_GET['docid']);
if (!empty($academicProfile)) {
    redirect_to("viewAcademicProfile.php?docid=$academicProfile->docid");
}


$errors2 = array();
$doctorName = Doctor::find_by_docid($_GET['docid']);
$count = AcaProfile::count_all($doctorName->empid);

if (isset($_POST['submit'])) {

    $newAcademicProfile = new AcaProfile();
    $newAcademicProfile->docid = $_GET['docid'];
    $newAcademicProfile->empid = $doctorName->empid;
    $newAcademicProfile->name = $doctorName->name;
    $newAcademicProfile->id = ++$count;

    $newAcademicProfile->media = trim($_POST['media']);

    $trimmed_array = array_filter(array_map('trim', $_POST['journal']));
    if (!empty($trimmed_array)) {
        $journal = implode(',', $trimmed_array);
        $newAcademicProfile->journal = $journal;
    }

    $trimmed_array = array_filter(array_map('trim', $_POST['subscription']));
    if (!empty($trimmed_array)) {
        $subscription = implode(',', $trimmed_array);
        $newAcademicProfile->subscription = $subscription;
    }

    $newAcademicProfile->materials = trim($_POST['materials']);
    $newAcademicProfile->activities = trim($_POST['activities']);
    $newAcademicProfile->local = trim($_POST['local']);
    $newAcademicProfile->intern = trim($_POST['intern']);


    if (empty($errors2)) {
        $rowCount = AcaProfile::find_by_docid($newAcademicProfile->docid);
        if (empty($rowCount)) {
            if ($newAcademicProfile->create()) {
                // Success
                $message = "Added Succesfully.";
                redirect_to('AddAcademicProfile.php');
            }
        } else {
            array_push($errors2, "Doctor details already exist");
        }
    }
}

$pageTitle = "Add Academic Profile";
require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Academic Profile<small><?php echo "  ".$doctorName->name?></small></h1>
    </div>                <!-- /.col-lg-12 -->
</div>
<div class="row">
    <ul>
        <?php foreach ($errors2 as $val) { ?>
            <li style="color:red"><?php echo $val; ?></li>
        <?php } ?>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-sx-12 ">
        <form action="academicProfile.php?docid=<?php echo $doctorName->docid; ?>" method="post">  
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                    <label>Prefered Media</label>
                    <select name="media" class="form-control" style="width:70%" >
                        <option value="0" >Select Media</option>
                        <option value='Print' <?php
                        if (isset($_POST['media']) && $_POST['media'] == 'Print') {
                            echo "selected";
                        }
                        ?> >Print</option>
                        <option value='Electronic' <?php
                        if (isset($_POST['media']) && $_POST['media'] == 'Electronic') {
                            echo "selected";
                        }
                        ?> >Electronic</option>
                    </select> 
                </div>
            </div>

            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Scientific Journals</label>
                    <?php
                    if (isset($_POST['journal'])) {
                        foreach ($_POST['journal'] as $value) {
                            ?>
                            <input type="text" name="journal[]" value="<?php echo $value ?>" class="form-control customWidth" /></br>
                            <?php
                        }
                    } else {
                        ?>
                        <input type="text" name="journal[]" value="" class="form-control customWidth" /></br>
                        <input type="text" name="journal[]" value="" class="form-control customWidth" /></br>
                        <input type="text" name="journal[]" value="" class="form-control customWidth" /></br>
                    <?php } ?>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Online Subscription</label>
                    <?php
                    if (isset($_POST['subscription'])) {
                        foreach ($_POST['subscription'] as $value) {
                            ?>
                            <input type="text" name="subscription[]" value="<?php echo $value ?>" class="form-control customWidth" /></br>
                            <?php
                        }
                    } else {
                        ?>
                        <input type="text" name="subscription[]" value="" class="form-control customWidth" /></br>
                        <input type="text" name="subscription[]" value="" class="form-control customWidth" /></br>
                        <input type="text" name="subscription[]" value="" class="form-control customWidth" /></br>
                    <?php } ?>
                </div>
            </div>

            <div class="row"><div class="col-lg-12"><label>Interest In Patient Education Material</label></div></div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>(Pls specify therapy)</label>
                    <input type="text" name="materials" value="<?php
                    if (isset($_POST['materials'])) {
                        echo $_POST['materials'];
                    }
                    ?>"  class="form-control customWidth" placeholder="if more than one therapy put comma after every therapy" required/>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Activities (Pls specify)</label>
                    <input type="text" name="activities" value="<?php
                    if (isset($_POST['activities'])) {
                        echo $_POST['activities'];
                    }
                    ?>"  class="form-control customWidth" required />
                </div>				
            </div>

            <div class="row" >
                <div class="col-lg-12"><label>Professional Association</label></div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Local</label>
                    <input type="text" name="local" maxlength="30" value="<?php
                    if (isset($_POST['local'])) {
                        echo $_POST['local'];
                    }
                    ?>"   class="form-control customWidth" required />
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>National/International</label>
                    <td><input type="text" name="intern" maxlength="30" value="<?php
                        if (isset($_POST['intern'])) {
                            echo $_POST['intern'];
                        }
                        ?>"  class="form-control customWidth" required/>
                </div>				
            </div>
            <hr/>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2"> <input type="submit" name="submit" value="Save" class="btn btn-primary " /></div>
            </div>

        </form>
    </div>
</div>
<?php require_once("layouts/TMfooter.php"); ?>