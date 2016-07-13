<?php
ini_set('max_execution_time', 30000000);
require_once(dirname(__FILE__) . "/includes/initialize.php");
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
$employees = QueryWrapper::executeQuery($sql);
?>
<table>
    <tr>
        <th>TM Name</th>
        <td>HQ</td>
        <th>SM Name</th>
        <th>BM Name</th>
        <td>First Name</td>
        <td>Middle Name</td>
        <td>Last Name</td>
        <td>MSL Code</td>
        <td>Speciality</td>
        <td>Qualification</td>
        <td>Class</td>
        <td>Visit Frequency</td>
        <td>Gender</td>
        <td>Mobile No</td>
        <td>Phone No</td>
        <td>Emailid</td>
        <td>Date Of Birth</td>
        <td>Date of Anniversary</td>
        <td>Clinic Address</td>
        <td>Pin code</td>
        <td>Residential Address </td>
        <td>Pin code</td>
        <td>Doctor Potential</td>
        <td>Current Business</td>
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
                <td></td>
                <td></td>
                <td><?php echo $item->msl_code; ?></td>
                <td><?php echo $item->speciality ?></td>
                <td></td>
                <td><?php echo $item->class ?></td>
                <td></td>
                <td></td>
                <td><?php echo $item->mobile ?></td>
                <td></td>
                <td><?php echo $item->emailid ?></td>
                <td><?php
                    if ($item->DOB == '0000-00-00' || $item->DOB == '' || is_null($item->DOB)) {
                        echo '';
                    } else {
                        echo date('d-m-Y', strtotime($item->DOB));
                    }
                    ?>
                </td>
                <td><?php
                    if ($item->DOA == '0000-00-00' || $item->DOA == '' || is_null($item->DOA)) {
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
                <td></td>
                <td><?php echo $item->doctor_id; ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>