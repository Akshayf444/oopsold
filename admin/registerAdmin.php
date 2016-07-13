<?php 
session_start();
if (!isset($_SESSION['admin']))
    {   header('Location:login.php'); }
?>
<?php require_once("../includes/initialize.php");?>

<?php
$errors=array();
$date1="";
$date2="";

if(isset($_POST['submit'])) {
	  // validations
 // $required_fields = array($_POST);
 	// $result.=validate_presences($required_fields);

    $newUser = new User();
    //validating name
    if(!empty($_POST['name'])){ 
    	$newUser->name= trim($_POST['name']);
    }
    else{array_push($errors, "Name cant be blank");}
  

  //validating email id
    if(!empty($_POST['emailid'])){ 
		if (filter_var($_POST['emailid'], FILTER_VALIDATE_EMAIL)){
			$newUser->emailid= trim($_POST['emailid']);
		}
		else{
			array_push($errors, "Invalid Email Address");
		}
     }
    else{array_push($errors, "Email-id cant be blank");}
     
   //validating password  
    if(!empty($_POST['password'])){  
	   	if (strlen($_POST['password']) < 6 || strlen($_POST['password']) >=15) {
    		array_push($errors,"Password length must be less than 15 and more than 6 characters");
    	}
    	
    	else{
    		$newUser->password= trim($_POST['password']);
    	}
    }else{array_push($errors, "Password cant be blank");}

    //validating mobile no
   	 if(!empty($_POST['mobile'])){  
		if(preg_match("/^((\+){0,1}91(\s){0,1}(\-){0,1}(\s){0,1}){0,1}9[0-9](\s){0,1}(\-){0,1}(\s){0,1}[1-9]{1}[0-9]{7}$/",$newUser->mobile)){
			$newUser->mobile= trim($_POST['mobile']);
		}
    	else{
    		$newUser->mobile= trim($_POST['mobile']);
    	}
	}else{array_push($errors, "Mobile no cant be blank");}    

 
   	//Date conversion
      $dob=$_POST['DOB'];
      $newUser->DOB=date("Y-m-d",strtotime($dob));
      //date conversion
      $doa=$_POST['DOA'];
      $date = date("Y-m-d",strtotime($doa));
      $newUser->DOA=$date;

      //validating password
    if(!empty($_POST['address'])){
		 $newUser->address=trim( $_POST['address']);}
    else{array_push($errors, "Address cannot be blank");}

	 //validating address
	if(!empty($_POST['city'])){   $newUser->city=trim( $_POST['city']);}
    else{array_push($errors, "City Name cant be blank");}

      $newUser->state=trim($_POST['state']);
   	
  	//validating data
   //	$fields_with_max_lengths = array($_POST['mobile'] => 10,$_POST['address'] => 50,$_POST['address']=> 50);
  	//$result.=validate_max_lengths($fields_with_max_lengths);
  	
  if(empty($errors)){
  	$rowCount=User::find_by_emailid($newUser->emailid);
  	if(empty($rowCount)){
		    if($newUser->create()) {
		      // Success
		      $message = "Registered Succesfully.";
		      redirect_to('login.php');
		    } else {
		      // Failure
		    	echo "failed";
		     // $message = join("<br />", $newUser->errors);
		    }
		}
	else{
		array_push($errors, "User already exist");

	}	    
	}


    
  }
  ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Basic Profile</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">
<script>
$(function() {
$( "#datepicker" ).datepicker({dateFormat : 'yy-mm-dd'});
$( "#datepicker1" ).datepicker({dateFormat : 'yy-mm-dd'});
});
</script>
</head>
<body>
<!-- Showing errors-->
	<div>
    <?php foreach($errors as $val){ ?>
        <p style="color:red"><?php echo $val; ?></p>
    <?php } ?>
	</div>

	<a href="index.php">Back</a>
		<h2>Registration</h2>
		<?php //echo output_message($message); ?>

		<form action="registerAdmin.php" method="post" >
		  <table>
		  	 <tr>
		      <td>Name</td>
		      <td>
		        <input type="text" name="name" maxlength="30" value=""  />
		      </td>
		    </tr>
		    <tr>
		      <td>Email-id:</td>
		      <td>
		        <input type="text" name="emailid" maxlength="30" value=""  />
		      </td>
		    </tr>
		    <tr>
		      <td>Password:</td>
		      <td>
		        <input type="password" name="password" maxlength="30" value=""   />
		      </td>

		    </tr>
		     <tr>
		      <td>Mobile:</td>
		      <td>
		        <input type="text" name="mobile" maxlength="10" value=""   />
		      </td>
		    </tr>
		    <tr>
		      <td>Date of Birth:</td>
		      <td>
		        <input type="text" name="DOB"  value="" id="datepicker"  />
		      </td>
		    </tr>
		    <tr>
		      <td>Date Of Aniversary:</td>
		      <td>
		        <input type="text" name="DOA" value="" id="datepicker1"  />
		      </td>
		    </tr>
		    <tr>
		      <td>Address:</td>
		      <td>
		        <input type="text" name="address" maxlength="50" value=""  />
		      </td>
		    </tr>
		    <tr>
		      <td>City:</td>
		      <td>
		        <input type="text" name="city" maxlength="30" value=""   />
		      </td>
		    </tr>
		    <tr>
      		<td>State:</td>
      		<td> 
				<select name="state">
				<option value='Andaman and Nicobar Islands'>Andaman and Nicobar Islands</option>
				<option value='Andhra Pradesh'>Andhra Pradesh</option>
				<option value='Arunachal Pradesh'>Arunachal Pradesh</option>
				<option value='Assam'>Assam</option>
				<option value='Bihar'>Bihar</option>
				<option value='Chandigarh'>Chandigarh</option>
				<option value='Chhattisgarh'>Chhattisgarh</option>
				<option value='Dadra and Nagar Haveli'>Dadra and Nagar Haveli</option>
				<option value='Daman and Diu'>Daman and Diu</option>
				<option value='Delhi'>Delhi</option>
				<option value='Goa'>Goa</option>
				<option value='Gujarat'>Gujarat</option>
				<option value='Haryana'>Haryana</option>
				<option value='Himachal Pradesh'>Himachal Pradesh</option>
				<option value='Jammu and Kashmir'>Jammu and Kashmir</option>
				<option value='Jharkhand'>Jharkhand</option>
				<option value='Karnataka'>Karnataka</option>
				<option value='Kerala'>Kerala</option>
				<option value='Lakshadweep'>Lakshadweep</option>
				<option value='Madhya Pradesh'>Madhya Pradesh</option>
				<option value='Maharashtra'>Maharashtra</option>
				<option value='Manipur'>Manipur</option>
				<option value='Meghalaya'>Meghalaya</option>
				<option value='Mizoram'>Mizoram</option>
				<option value='Nagaland'>Nagaland</option>
				<option value='Odisha'>Odisha</option>
				<option value='Puducherry'>Puducherry</option>
				<option value='Punjab'>Punjab</option>
				<option value='Rajasthan'>Rajasthan</option>
				<option value='Sikkim'>Sikkim</option>
				<option value='Tamil Nadu'>Tamil Nadu</option>
				<option value='Tripura'>Tripura</option>
				<option value='Uttar Pradesh'>Uttar Pradesh</option>
				<option value='Uttarakhand'>Uttarakhand</option>
				<option value='West Bengal'>West Bengal</option>
				</select> 
     		 </td>
     		 </tr>
		    <tr>
		      <td>
		        <input type="submit" name="submit" value="Register" />
		      </td>
		    </tr>
		  </table>
		</form>
	</body>
	</html>

