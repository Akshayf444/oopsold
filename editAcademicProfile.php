<?php
require_once(dirname(__FILE__) . "/includes/initialize.php");

session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

$academicProfile = AcaProfile::find_by_docid($_GET['docid']);
$doctorName = Doctor::find_by_docid($_GET['docid']);
$errors2 = array();

if (isset($_POST['submit'])) {
    $newAcademicProfile = new AcaProfile();

    $newAcademicProfile->docid = $_GET['docid'];
    $newAcademicProfile->empid = $academicProfile->empid;
    $newAcademicProfile->name = $doctorName->name;
    $newAcademicProfile->id = $academicProfile->id;

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
        $newAcademicProfile->update($_GET['docid']);
        redirect_to("viewAcademicProfile.php?page=$newAcademicProfile->id");
    }
}

$pageTitle = "Edit Academic Profile";
require_once("layouts/TMheader.php");
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Edit Academic Profile<small><?php echo "  ".$doctorName->name?></small></h1>
    </div>            <!-- /.col-lg-12 -->
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
        <form action="editAcademicProfile.php?docid=<?php echo $doctorName->docid; ?>" method="post">
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12 ">
                    <label>Preferred Media</label>
                    <option value="0" >Select Media</option>
                    <select name="media" class="form-control" style="width:70%" >
                        <option value='Print' <?php
                        if ($academicProfile->media == 'Print') {
                            echo 'selected';
                        }
                        ?>>Print</option>
                        <option value='Electronic' <?php
                        if ($academicProfile->media == 'Electronic') {
                            echo 'selected';
                        }
                        ?>>Electronic</option>
                    </select> 
                </div>
            </div>

            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Scientific Journals</label>
                    <?php
                    $Journals = explode(",", $academicProfile->journal);
                    $lenght = count($Journals);
                    $Journals = array_values(array_filter($Journals));
                    foreach ($Journals as $journal) {
                        ?>
                        <input type="text" name="journal[]" value="<?php echo $journal; ?>" class="form-control customWidth" /></br>	
                        <?php
                    }
                    if ($lenght == 2) {
                        ?>
                        <input type="text" name="journal[]" value="" class="form-control customWidth" /></br>	
                    <?php } ?>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Online Subscription</label>
                    <?php
                    $Subscriptions = explode(",", $academicProfile->subscription);
                    $lenght = count($Subscriptions);
                    $Subscriptions = array_values(array_filter($Subscriptions));
                    foreach ($Subscriptions as $Subscription) {
                        ?>
                        <input type="text" name="subscription[]" value="<?php echo $Subscription; ?>" class="form-control customWidth"  /></br>
<?php } if ($lenght == 2) { ?>
                        <input type="text" name="subscription[]" value="" class="form-control customWidth"  /></br>

<?php } ?>
                </div>
            </div>

            <div class="row"><div class="col-lg-12"><label>Interest In Patient Education Material</label></div></div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>(Pls specify therapy)</label>
                    <input type="text" name="materials" value="<?php echo $academicProfile->materials; ?>"  class="form-control customWidth" placeholder="if more than one therapy put comma after every therapy" required/>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Activities (Pls specify)</label>
                    <input type="text" name="activities" value="<?php echo $academicProfile->activities; ?>"  class="form-control customWidth" required/>
                </div>				
            </div>

            <div class="row" >
                <div class="col-lg-12"><label>Professional Association</label></div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Local</label>
                    <input type="text" name="local" maxlength="30" value="<?php echo $academicProfile->local; ?>"   class="form-control customWidth" required />
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>National/International</label>
                    <td><input type="text" name="intern" maxlength="30" value="<?php echo $academicProfile->intern; ?>"  class="form-control customWidth" required/>
                </div>				
            </div>
            <hr/>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2"> <input type="submit" name="submit" value="Save" class="btn btn-primary " /></div>
            </div>  
        </form>
    </div>
</div>
<?php
require_once("layouts/TMfooter.php");
