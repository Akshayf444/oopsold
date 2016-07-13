<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
ini_set('max_execution_time', 30000000);
//require_once(dirname(__FILE__) . "/includes/initialize.php");
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

$per_page = 50;

$total_count = Doctor::all();
$pagination = new Pagination($page, $per_page, $total_count);

$sql = "SELECT xx.name AS SM_NAME,yy.name AS BM_NAME,zz.name AS TM_NAME ,zz.HQ,d.name AS DOCTOR_NAME,d.docid AS doctor_id, d.*,db.*
            FROM (
             SELECT * FROM employees    
            ) AS zz
            LEFT JOIN (
                SELECT * FROM bm
            ) AS yy ON zz.bm_empid = yy.bm_empid
            LEFT  JOIN (
               SELECT * FROM sm
            ) AS  xx ON yy.sm_empid = xx.sm_empid
            INNER JOIN (
		SELECT * FROM doctors WHERE `is_delete` = 0
            ) AS d ON zz.empid = d.`empid`
            LEFT JOIN `doc_basic_profile` db
	ON d.`docid` = db.`docid` 

        GROUP BY d.`docid` ";
$sql .= " LIMIT {$per_page} ";
$sql .= " OFFSET {$pagination->offset()}";
$employees = QueryWrapper::executeQuery($sql);

require_once("adminheader.php");
?>
<div class="row">
    <div class="col-lg-6">
<table class="table table-bordered" style="font-size: 11px;">
    <tr>
        <th>TM Name</th>
        <th>HQ</th>
        <th>SM Name</th>
        <th>BM Name</th>
        <th>First Name</th>
<!--        <th>Middle Name</th>
        <th>Last Name</th>-->
        <th>MSL Code</th>
        <th>Speciality</th>
<!--        <th>Qualification</th>-->
        <th>Class</th>
        <th>Visit Frequency</th>
<!--        <th>Gender</th>-->
        <th>Mobile No</th>
<!--        <th>Phone No</th>-->
        <th>Emailid</th>
        <th>Date Of Birth</th>
        <th>Date of Anniversary</th>
        <th>Clinic Address</th>
        <th>Pin code</th>
        <th>Residential Address </th>
        <th>Pin code</th>
        <th>Doctor Potential</th>
<!--        <td>Current Business</td>-->
    </tr>
    <?php
    if (!empty($employees)) {
        foreach ($employees as $item) {
            $address1 = array($item->plot1, $item->street1, $item->area1, $item->city1, $item->state1);
            $address1 = array_filter(array_map('trim', $address1));
            $address1 = join(",", $address1);
            $address2 = array($item->plot2, $item->street2, $item->area2, $item->city2, $item->state2);
            $address2 = array_filter(array_map('trim', $address2));
            $address2 = join(",", $address2);
            ?>
            <tr>
                <td><?php echo $item->TM_NAME ?></td>
                <td><?php echo $item->HQ ?></td>
                <td><?php echo $item->SM_NAME ?></td>
                <td><?php echo $item->BM_NAME ?></td>
                <td><?php echo $item->DOCTOR_NAME ?></td>
<!--                <td></td>
                <td></td>-->
                <td><?php echo $item->msl_code; ?></td>
                <td><?php echo $item->speciality ?></td>
<!--                <td></td>-->
                <td><?php echo $item->class ?></td>
                <td></td>
<!--                <td></td>-->
                <td><?php echo $item->mobile ?></td>
<!--                <td></td>-->
                <td><?php echo $item->emailid ?></td>
                <td><?php
        if ($item->DOB == '0000-00-00') {
            echo '';
        } else {
            echo date('d-m-Y', strtotime($item->DOB));
        }
            ?>
                </td>
                <td><?php
            if ($item->DOA == '0000-00-00') {
                echo '';
            } else {
                echo date('d-m-Y', strtotime($item->DOA));
            }
            ?>
                </td> 

                <td><?php echo $address1 ?></td>
                <td><?php echo $item->pincode1 ?></td>
                <td><?php echo $address2 ?></td>
                <td><?php echo $item->pincode2 ?></td>
                <td><?php echo $item->pharma_potential ?></td>
<!--                <td></td>-->
<!--                <td><?php echo $item->doctor_id; ?></td>-->
            </tr>
            <?php
        }
    }
    ?>
</table>
        </div>
</div>
<div class ="row">
    <div class="result">

        <div class="col-lg-12" style="clear: both; margin-bottom: 5px;text-align: center">
            <?php
            $limitpage = 10;
            if (isset($pagination) && !isset($_GET['company']) && !isset($_GET['therapy']) && !isset($_GET['subtherapy']) && !isset($_GET['search'])) {
                $nextFivePages = array($page + 1, $page + 2, $page + 3, $page + 4, $page + 5);
                $previousPages = array($page - 5, $page - 4, $page - 3, $page - 2, $page - 1);
                if ($pagination->total_pages() > 1) {


                    echo '<ul class="pagination pagination-sm">';
                    if ($pagination->has_previous_page()) {
                        echo "<li><a href=\"doctorwiseReport.php?page={$pagination->previous_page()}\">Previous</a></li> ";
                    }
                    if ($previousPages[0] > 1) {
                        echo "<li><a href=\"doctorwiseReport.php?page=1\">First</a></li> ";
                    }

                    if (!empty($previousPages)) {
                        foreach ($previousPages as $pages) {
                            if ($pages > 0) {
                                echo "<li><a href=\"doctorwiseReport.php?page={$pages}\">{$pages}</a></li> ";
                            }
                        }
                    }

                    echo "<li class='active'><a href=\"doctorwiseReport.php?page={$page}\">{$page}</a></li> ";

                    if (!empty($nextFivePages)) {
                        foreach ($nextFivePages as $pages) {
                            if ($pages <= $pagination->total_pages()) {
                                echo "<li><a href=\"doctorwiseReport.php?page={$pages}\">{$pages}</a></li> ";
                            }
                        }
                    }

                    if (end($nextFivePages) < $pagination->total_pages()) {
                        echo "<li><a href=\"doctorwiseReport.php?page={$pagination->total_pages()}\">Last</a></li> ";
                    }
                    if ($pagination->has_next_page()) {
                        echo "<li><a href=\"doctorwiseReport.php?page={$pagination->next_page()}\">Next</a></li> ";
                    }
                    echo '</ul>';
                }
            }
            ?>

        </div>
    </div>
</div>
<?php
require_once("adminfooter.php");
