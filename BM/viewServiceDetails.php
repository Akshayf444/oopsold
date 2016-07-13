<?php session_start();
if(!isset($_SESSION['employee'])){header("Location:login.php");}

 require_once(dirname(__FILE__)."/includes/initialize.php");
 //require_once(dirname(__FILE__)."/includes/DBconnection.php");
$employee=Employee::find_by_empid($_SESSION['employee']);
 $service=Services::find_by_docid($_GET['docid']); 
	$doctorName=Doctor::find_by_docid($_GET['docid']);
	$empid=$doctorName->empid;

//Next Button
	$BProfile=Services::count_all($empid);
	$id=$service->id + 1;
	global $database;
	if($id <= $BProfile){
	$sql="SELECT * FROM services WHERE id='$id' AND empid='{$_SESSION['employee']}' ";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);
	$nextId=$row[0];
	$first=false;
	}
	else{
		$id=1;
		$sql="SELECT * FROM services WHERE id='$id' AND empid='{$_SESSION['employee']}' ";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);
		$nextId=$row[0];
		$first=true;
	}
	
	//$BProfile=service::count_all($empid);
	$id2=$service->id - 1;
	if($id2 >=1){
	$sql="SELECT * FROM services WHERE id='$id2' AND empid='{$_SESSION['employee']}'  ";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);
	$previousId=$row[0];
	$last=false;
	}
	else{

		$id2=$service->id;
		$sql="SELECT * FROM services WHERE id='$id2' AND empid='{$_SESSION['employee']}'  ";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);
		$previousId=$row['0'];
		$last=true;
	}


	$pageTitle="View Service Profile";
  require_once("layouts/TMheader.php");
?>

<div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Service Profile</h1>
        </div>      
</div>
<div class ="row" style="margin-bottom:1em">
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
	<!--Next and Previous Buttons-->
	 <a href="viewServiceDetails.php?docid=<?php echo $nextId; ?>" > <input type="button" value="Next" style="display:<?php if($first==true){echo "none";}?>"  class="btn btn-default"/></a><br/>
	</div>
	<div class="col-lg-8 col-sm-8 col-md-8 col-xs-8"></div>
	<div class ="col-lg-2 col-sm-2 col-md-2 col-xs-2 " style="text-align:right">
 	<a href="viewServiceDetails.php?docid=<?php echo $previousId; ?>" > <input type="button" value="Previous" style="display:<?php
 	if($last==true){echo "none";} ?>"  class="btn btn-default" /></a><br/>
 	</div>
</div>

<div class ="row">
	<div class="col-lg-8 col-sm-8 col-md-8 col-xs-8">
		<div class="table-responsive">
		<h2>Doctor Name : <?php echo $doctorName->name;?></h2>
		<table class="viewProfile">
		    <tr>
		      <td>Aushadh:</td>
		      <td>
		       <?php echo $service->aushadh; ?>
		      </td>
		    </tr>
		    <tr>
		      <td>Other Services:</td>
		      <td>
		       <?php echo $service->other_services; ?>
		      </td>
		    </tr>

		    <tr>
				<td colspan="2">Services By Competing Companies</td>
			</tr>
			        <tr>
			            <td>AOIC : </td>	
							<td><?php echo $service->AOIC; ?></td>
			        </tr>
			        <tr>
			            <td>DOC </td>
						<td><?php echo $service->DOC; ?></td>
            		</tr>
            		<tr>
			            <td>ESCRS </td>
							<td><?php echo $service->ESCRS; ?></td>
            		</tr>
            		<tr>
			            <td>WGC </td>
							<td><?php echo $service->WGC; ?></td>
            		</tr>
            		<tr>
			            <td>WOC </td>
			            <td><?php echo $service->WOC; ?></td>
            		</tr>
            		<tr>
			            <td>Other </td>
							<td><?php echo $service->Other; ?></td>
            		</tr>
		</table>
		</div>
 	</div>
</div>		
<div class="row" style="padding:1em">
<a href="editService.php?docid=<?php echo $service->docid; ?>"> <input type="button" value="Edit Service" title="Edit Service" style="display:<?php if ($employee->lock_service == 1){echo "none"; } ?>" class="btn btn-primary" /></a><br/>
</div>
<?php  require_once("layouts/TMfooter.php");