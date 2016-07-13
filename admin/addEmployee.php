<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$errors = array();

$newEmployee = new Employee();

$newEmployee->empid = $newEmployee->autoGenerate_id();

if (isset($_POST['submit'])) {

    //validating name
    if (!empty($_POST['empid'])) {
        $newEmployee->empid = trim($_POST['empid']);
    }

    if (!empty($_POST['name'])) {
        $newEmployee->name = trim($_POST['name']);
    } else {
        array_push($errors, "Name cant be blank");
    }


    //validating email id
    if (!empty($_POST['emailid'])) {
        if (filter_var($_POST['emailid'], FILTER_VALIDATE_EMAIL)) {
            $newEmployee->emailid = trim($_POST['emailid']);
        } else {
            array_push($errors, "Invalid Email Address");
        }
    } else {
        array_push($errors, "Email-id cant be blank");
    }

    //validating password  
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6 || strlen($_POST['password']) >= 15) {
            array_push($errors, "Password length must be less than 15 and more than 6 characters");
        } else {
            $newEmployee->password = trim($_POST['password']);
        }
    } else {
        array_push($errors, "Password cant be blank");
    }

    //validating mobile no
    if (!empty($_POST['mobile'])) {
        if (preg_match("/^((\+){0,1}91(\s){0,1}(\-){0,1}(\s){0,1}){0,1}9[0-9](\s){0,1}(\-){0,1}(\s){0,1}[1-9]{1}[0-9]{7}$/", $newEmployee->mobile)) {
            $newEmployee->mobile = trim($_POST['mobile']);
        } else {
            $newEmployee->mobile = trim($_POST['mobile']);
        }
    } else {
        array_push($errors, "Mobile no cant be blank");
    }


    //validating address
    if (!empty($_POST['city'])) {
        $newEmployee->city = trim($_POST['city']);
    } else {
        array_push($errors, "City Name cant be blank");
    }

    $newEmployee->zone = trim($_POST['zone']);
    $newEmployee->region = trim($_POST['region']);
    $newEmployee->HQ = trim($_POST['HQ']);
    $newEmployee->state = trim($_POST['state']);

    //validating data
    //	$fields_with_max_lengths = array($_POST['mobile'] => 10,$_POST['address'] => 50,$_POST['address']=> 50);
    //$result.=validate_max_lengths($fields_with_max_lengths);

    if (empty($errors)) {
        $rowCount = Employee::find_by_empid($newEmployee->empid);
        if (empty($rowCount)) {
            if ($newEmployee->create()) {
                // Success
                $message = "Employee " . $newEmployee->name . "Added Succesfully.";
                redirect_to('addEmployee.php');
            } else {
                // Failure
                echo "failed";
                // $message = join("<br />", $newEmployee->errors);
            }
        } else {
            array_push($errors, "Employee already exist");
        }
    }
}
?>


<!-- Showing errors-->
<div>
<?php foreach ($errors as $val) { ?>
        <p style="color:red"><?php echo $val; ?></p>
<?php } ?>
</div>


<h2>Add Employee</h2>
<?php //echo output_message($message);  ?>

<form action="addEmployee.php" method="post" >
    <table>
        <tr>
            <td>Employee Id</td>
            <td>
                <input type="text" name="empid" maxlength="30" readonly 
                       value="<?php echo $newEmployee->empid; ?>" />
            </td>
        </tr>
        <tr>
            <td>Name</td>
            <td>
                <input type="text" name="name" maxlength="30" value=""  />
            </td>
        </tr>
        <tr>
            <td>Email-id:</td>
            <td>
                <input type="text" name="emailid" maxlength="50" value=""  />
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
                <input type="text" name="mobile" maxlength="14" value=""   />
            </td>
        </tr>
        <tr>
            <td>City:</td>
            <td>
                <input type="text" name="city" maxlength="30" value=""   />
            </td>
        </tr>
        <tr>
        <tr>
            <td>Zone:</td>
            <td>
                <input type="text" name="zone" maxlength="30" value=""   />
            </td>

        </tr>
        <tr>
            <td>Region:</td>
            <td>
                <input type="text" name="region" maxlength="14" value=""   />
            </td>
        </tr>
        <tr>
            <td>HQ:</td>
            <td>
                <input type="text" name="HQ" maxlength="30" value=""   />
            </td>
        </tr>

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
                <input type="submit" name="submit" value="Add Employee" />
            </td>
        </tr>
    </table>
</form>

