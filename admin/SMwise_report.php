<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:logout.php");
}
require_once("../includes/initialize.php");
//ini_set('max_execution_time', 30000000);

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
require_once("adminheader.php");
echo pageHeading("SM Wise Profile Count");

function ProfileCount($empid) {
    $sql = " SELECT 
                   COUNT(d.docid) AS DOCTOR_COUNT, COUNT(da.`docid`) AS academic , COUNT(ser.`docid`) service , COUNT(`cmpt`.`docid`) compt,
                   COUNT(db.`docid`) AS basic,SUM(CASE WHEN d.docid IS NOT NULL AND db.`docid` IS NOT NULL AND da.`docid` IS NOT NULL AND ser.`docid` IS NOT NULL AND `cmpt`.`docid` IS NOT NULL THEN 1 ELSE 0 END) AS completed
                   FROM `employees` e 
                   LEFT JOIN doctors d 
                   ON d.`empid` = e.`empid`
                       LEFT JOIN services ser 
                       ON d.`docid` = ser.`docid` 
                     LEFT JOIN `doc_academic_profile` da 
                       ON d.`docid` = da.`docid` 
                     LEFT JOIN competitors cmpt 
                       ON d.`docid` = cmpt.`docid` 
                       LEFT JOIN `doc_basic_profile` db 
                    ON d.`docid` = db.`docid` 
     AND db.`activity_inclination` != '' 
                AND db.`area1` != '' 
                AND db.`area2` != '' 
                AND db.`behaviour` != '' 
                AND db.`class` != '' 
                AND db.`clinic_name` != '' 
                AND db.`cornea` != '' 
                AND db.`daily_opd` != '' 
                AND db.`DOB` != '0000-00-00' 
                AND db.`gen_ophthal` != '' 
                AND db.`glaucoma` != '' 
                AND db.`hobbies` != '' 
                AND db.`inclination_to_speaker` != '' 
                AND db.`msl_code` != '' 
                AND db.`pharma_potential` != '' 
                AND db.`potential_to_speaker` != '' 
                AND db.`receive_mailers` != '' 
                AND db.`receive_sms` != '' 
                AND db.`retina` != '' 
                AND db.`state1` != '' 
                AND db.`state2` != '' 
                AND db.`total` != '' 
                AND db.`type` != '' 
                AND db.`value_per_rx` != '' 
                AND db.`yrs_of_practice` != '' 
                AND db.`city1` != '' 
                AND db.`city2` != '' 
                       WHERE d.is_delete = 0 AND d.`empid` = {$empid}";
    $result_array = QueryWrapper::executeQuery($sql);
    return !empty($result_array) ? array_shift($result_array) : FALSE;
}
?>

<div class="row">
    <div class="col-lg-12">
        <a download="SMwiseReport.xls" class="btn btn-success" href="#" onclick="return ExcellentExport.excel(this, 'datatable', 'Sheeting');">Export to Excel</a>
    </div>
</div>
<table class="table table-bordered" id="datatable">

    <tr>
        <th>SM ID</th>
        <th>SM Name</th>
        <th>BM ID</th>
        <th>BM Name</th>
        <th>TM ID</th>
        <th>TM Name</th>
        <th>Doctor Count </th>
        <th>BAsic Count </th>
        <th>Service Count</th>
        <th>Academic Count</th>
        <th>Compt Count</th>
        <th>Total Completed Profiles</th>
    </tr>

    <?php
    if (!empty($employees)) {
        foreach ($employees as $item) {
            ?>
            <tr>
                <td><?php echo $item->sm_empid; ?></td>
                <td><?php echo $item->SM_NAME; ?></td>
                <td><?php echo $item->BM_ID; ?></td>
                <td><?php echo $item->BM_NAME; ?></td>
                <td><?php echo $item->TM_ID; ?></td>
                <td><?php echo $item->TM_NAME; ?></td>
                <td><?php
        $profileCount = ProfileCount($item->empid);
        echo $profileCount->DOCTOR_COUNT;
            ?></td>
                <td><?php echo $profileCount->basic; ?></td>
                <td><?php echo $profileCount->service; ?></td>
                <td><?php echo $profileCount->academic; ?></td>
                <td><?php echo $profileCount->compt; ?></td>
                <td><?php echo $profileCount->completed; ?></td>
            </tr>
            <?php
        }
    }
    ?>

</table>
<script src="http://techvertica.in/jardiance/js/excellentexport.min.js" type="text/javascript"></script>
<?php require_once("adminfooter.php"); ?>