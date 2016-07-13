<?php
ini_set('max_execution_time', 30000000);
require_once(dirname(__FILE__) . "/includes/initialize.php");
$sql = "SELECT xx.sm_empid,xx.name AS SM_NAME,yy.bm_empid AS BM_ID,yy.name AS BM_NAME,zz.empid,zz.cipla_empid AS TM_ID,zz.name AS TM_NAME 
            FROM (
             SELECT * FROM employees    
            ) AS zz
            LEFT JOIN (
                SELECT * FROM bm
            ) AS yy ON zz.bm_empid = yy.bm_empid
            LEFT  JOIN (
               SELECT * FROM sm
            ) AS  xx ON yy.sm_empid = xx.sm_empid

            GROUP BY zz.empid  ";

$employees = QueryWrapper::executeQuery($sql);

echo pageHeading("SM Wise Profile Count")
?>

<div class="col-lg-12">
    <table>
        <tr>
            <th>SM ID</th>
            <th>SM Name</th>
            <th>BM ID</th>
            <th>BM Name</th>
            <th>TM ID</th>
            <th>TM Name</th>
            <td>Doctor Count </td>
            <td>BAsic Count </td>
            <td>Service Count</td>
            <td>Academic Count</td>
            <td>Compt Count</td>
            <td>Total Completed Profiles</td>
        </tr>
        <?php
        if (!empty($employees)) {
            foreach ($employees as $item) {
                ?>
                <tr>
                    <td><?php echo $item->sm_empid ?></td>
                    <td><?php echo $item->SM_NAME ?></td>
                    <td><?php echo $item->BM_ID ?></td>
                    <td><?php echo $item->BM_NAME ?></td>
                    <td><?php echo $item->TM_ID ?></td>
                    <td><?php echo $item->TM_NAME ?></td>
                    <td><?php $profileCount = Doctor::allProfileCount($item->empid);
        echo $profileCount->DOCTOR_COUNT ?></td>
                    <td><?php echo $profileCount->basic; ?></td>
                    <td><?php echo $profileCount->service ?></td>
                    <td><?php echo $profileCount->academic ?></td>
                    <td><?php echo $profileCount->compt ?></td>
                    <td><?php echo $profileCount->completed ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>