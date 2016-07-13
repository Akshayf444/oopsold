<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:login.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");

$academicProfile = AcaProfile::find_by_docid($_GET['docid']);
$doctorName = Doctor::find_by_docid($_GET['docid']);
$empid = $doctorName->empid;
if (empty($academicProfile)) {
    die("Record Dosent Exist.");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Academic Profile</title>
    </head>
    <body>

        <h1>Name : <?php echo $doctorName->name; ?><h1>
                <h2>Your Academic Profile</h2>

                <table>
                    <tr>
                        <td>Prefered Academic Media:</td>
                        <td>
                            <?php echo $academicProfile->media; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Scientific Journal:</td>
                        <td>
                            <?php echo $academicProfile->journal; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Online Subscriptions:</td>
                        <td>
                            <?php echo $academicProfile->subscription; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Interest in patient education</td>

                        <td>
                            <tr>
                                <td>Materials</td>	
                                <td> <?php echo $academicProfile->materials; ?></td>
                            </tr>
                            <tr>
                                <td>Activities</td>	
                                <td><?php echo $academicProfile->activities; ?></td>
                            </tr>
                        </td>
                    </tr>

                    <tr>
                        <td>Professional Association</td>
                        <td>
                            <tr>
                                <td>Local:</td>
                                <td> <?php echo $academicProfile->local; ?></td>
                            </tr>
                            <tr>
                                <td>Intern :</td>
                                <td><?php echo $academicProfile->intern; ?></td>

                            </tr>

                    </tr>
                </table>

<!-- <a href="editAcademicProfile.php?docid=<?php echo $academicProfile->docid; ?>" > <input type="button" value="Edit Profile" title="Edit Profile" /></a><br/>-->
                </body>
                </html>