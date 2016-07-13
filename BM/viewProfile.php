<?php  session_start();
if(!isset($_SESSION['employee'])){header("Location:login.php");} 

 require_once(dirname(__FILE__)."/includes/initialize.php");
 //require_once(dirname(__FILE__)."/includes/DBconnection.php");

$pageTitle ="View Basic Profile";
$empName =Employee::find_by_empid($_SESSION['employee']);

$employee=Employee::find_by_empid($_SESSION['employee']);
 $basicProfile=BasicProfile::find_by_docid($_GET['docid']); 
	$doctorName=Doctor::find_by_docid($_GET['docid']);
	$empid=$doctorName->empid;
 
//Next Button
	$BProfile=BasicProfile::count_all($empid);
	$id=$basicProfile->id + 1;
	global $database;
	if($id <= $BProfile){
	$sql="SELECT * FROM doc_basic_profile WHERE id='$id' AND empid='{$_SESSION['employee']}' ";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);
	$nextId=$row[0];
	$first=false;
	}
	else{
		$id=1;
		$sql="SELECT * FROM doc_basic_profile WHERE id='$id' AND empid='{$_SESSION['employee']}'";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);
		$nextId=$row[0];
		$first=true;
	}
	
	

	//$BProfile=BasicProfile::count_all($empid);
	$id2=$basicProfile->id - 1;
	if($id2 >=1){
	$sql="SELECT * FROM doc_basic_profile WHERE id='$id2' AND empid='{$_SESSION['employee']}' ";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);	$previousId=$row[0];
	$last=false;
	}
	else{

		$id2=$basicProfile->id;
		$sql="SELECT * FROM doc_basic_profile WHERE id='$id2' AND empid='{$_SESSION['employee']}' ";
	$result=$database->query($sql);
	$row=$database->fetch_array($result);
		$previousId=$row['0'];
		$last=true;
	}
	require_once("layouts/TMheader.php");
?>

<div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Basic Profile</h1>
        </div>      
</div>

<div class ="row">
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
	 <a href="viewProfile.php?docid=<?php echo $nextId; ?>" > <input type="button" value="Next" style="display:
	 <?php if($first==true){echo "none";}?>" class="btn btn-default"/></a><br/>
	</div>
	<div class="col-lg-8 col-sm-8 col-md-8 col-xs-8"></div>
	<div class ="col-lg-2 col-sm-2 col-md-2 col-xs-2 " style="text-align:right">
 	<a href="viewProfile.php?docid=<?php echo $previousId; ?>" > <input type="button" value="Previous" style="display:<?php 
 			if($last==true){echo "none";} ?>"  class="btn btn-default" /></a><br/>
	</div>
</div>

<div class ="row">
	<div class="col-lg-8 col-sm-8 col-md-8 col-xs-8">
		<div class="table-responsive">
		<h2>Doctor Name : <?php echo $doctorName->name;?></h2>
		<table class="viewProfile">
		    <tr>
		      <td>Date of Birth:</td>
		      <td>
		       <?php echo $basicProfile->DOB; ?>
		      </td>
		    </tr>
		    <tr>
		      <td>Date Of Aniversary:</td>
		      <td>
		       <?php echo $basicProfile->DOA; ?>
		      </td>
		    </tr>

		    <tr>
		      <td>Class:</td>
		      <td>
		       <?php echo $basicProfile->class; ?>
		      </td>
		    </tr>

			<tr>
		    	<td>Want to recieve mailers</td>
		    	<td>
		    		<?php echo $basicProfile->receive_mailers; ?>
				</td>

			</tr>
			<tr>
		    	<td>Want to recieve SMS</td>
		    	<td>
		    		<?php echo $basicProfile->receive_sms; ?>
				</td>

			</tr>

			<tr>
		      <td>Years Of Practice:</td>
		      <td>
		       <?php echo $basicProfile->yrs_of_practice." Yrs ,"; echo  $basicProfile->month." Months";?>
		      </td>
		    </tr>

			<tr>
      		<td>Type of Doctor:</td>
      		<td> 
				<?php echo $basicProfile->type; ?>
     		 </td>
     		 </tr>

     	<tr>
      		<td>Behaviour of Doctor:</td>
      		<td> 
				<?php echo $basicProfile->behaviour; ?>
     		 </td>
     		 </tr>

			<tr>
		    	<td>Inclination To Speaker:</td>
		    	<td>
		    		<?php echo $basicProfile->inclination_to_speaker; ?>
				</td>

			</tr>
			<tr>
		    	<td>Potential To Speaker:</td>
		    	<td>
		    		<?php echo $basicProfile->potential_to_speaker; ?>
				</td>

			</tr>


		    <tr>
		    	<td>Clinic Address:</td>
		    	<td>
		    	<?php echo $basicProfile->clinic_address; ?>
			</td>
			</tr>

			<tr>
		    	<td>Residential Address:</td>
		    	<td>
		    	<?php echo $basicProfile->residential_address; ?>
				</td>
			</tr>


		<tr>
		    <td>Hobbies And Ineterst:</td>
		    <td>
				<?php echo $basicProfile->hobbies; ?>
			</td>
		</tr>
		<tr>
		    <td>Activity Inclination:</td>
		    <td>
				<?php echo $basicProfile->activity_inclination; ?></td>
			</tr>
		</tr>
		
		<tr>
		<td>Factors Inflencing Rxing Behaviour:</td>
      		<td> 
			<?php echo $basicProfile->factors; ?>
     		 </td>
     	</tr>
		<tr>
			<td colspan="2"><strong>Type Of Practice</strong></td>
		</tr>
		<tr>
		      <td>Gen Opthal:</td>
		      <td>
		        <?php echo $basicProfile->gen_ophthal; ?>
		      </td>
		 </tr>
		 <tr>
		      <td>Retina:</td>
		      <td>
		        <?php echo $basicProfile->retina; ?> 
		      </td>
		    </tr>
		    <tr>
		      <td>Glaucoma:</td>
		      <td>
		      <?php echo $basicProfile->glaucoma; ?>
		      </td>
		    </tr>
		    <tr>
		      <td>Cornea:</td>
		      <td>
		        <?php echo $basicProfile->cornea; ?>
		      </td>
		    </tr>
		    <tr>
		      <td>Any Other Plz Specify:</td>
		      <td>
		        <?php echo $basicProfile->other; ?>
		      </td>
		    </tr>
		    </td>
		    </tr>
		    <tr>
		      <td>Average Daily OPD:</td>
		      <td>
		        <?php echo $basicProfile->daily_opd;; ?>
		      </td>
		    </tr>
		    <tr>
		      <td>Average Value Per Rx:</td>
		      <td>
		        <?php echo $basicProfile->value_per_rx; ?>
		      </td>
		    </tr>
		    <tr>
		      <td>Pharma Potential:</td>
		      <td>
		        <?php echo $basicProfile->pharma_potential; ?>
		    </td>
		    </tr>
		  </table>

	</div>
	</div>
</div>
<div class="row" style="padding:1em">
 <a href="editBasicProfile.php?docid=<?php echo $basicProfile->docid; ?>" > <input type="button" value="Edit Profile" title="Edit Profile" style="display:<?php if ($employee->lock_basic == 1){echo "none"; } ?>" class="btn btn-primary" /></a><br/>

</div>
<?php require_once("layouts/TMfooter.php");