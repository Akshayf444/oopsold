<?php
session_start();
require_once("../includes/initialize.php");
$zone_id=$_SESSION['zone_id'];

$final=0;
  $bm_empid=$_SESSION['zone_id'];
	$bmName=zone::find_by_zoneid($bm_empid);


	$pageTitle ="View Team";
	$empcount=zone::view_all($bm_empid);
	$doctorCount=zone::count_all_doctors($bm_empid);
	//Total profiles for one BM
	$employees=zone::bm_name($bm_empid);
	$totalCount=zone::totalProfileCount($bm_empid);
  require_once("zoneheader.php");
 ?>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Your Team</h1>
  </div>                <!-- /.col-lg-12 -->
</div>
<div class="row">
  <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
    <div class="table-responsive">
    <table class="table table-bordered table-hover ">
      <tr>
        <th>BM Name</th>
        <th>Emp Name</th>
        <th>No of Doctors</th>
        <th>No of Profiles</th>
      </tr>
    <?php foreach($employees as $employee): ?>
      <tr>
        <td><?php echo $employee->name; ?></td>
        <td><?php echo $employee->emp_name; ?></td>
        <td><?php  echo $doctorCount=zone::count_all_doc($employee->empid); ?></td>
        <td><?php echo $doctors= Doctor::completed($employee->empid);?></td>

 	
    </tr>
    <?php endforeach; ?>
    </table>
  </div>
</div>
</div>
<?php require_once('zonefooter.php'); ?>