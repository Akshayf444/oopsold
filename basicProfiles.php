<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

//require_once(dirname(__FILE__) . "/includes/initialize.php");
require_once(dirname(__FILE__) . "/includes/class.QueryWrapper.php");
require_once(dirname(__FILE__) . "/includes/employee.php");
require_once(dirname(__FILE__) . "/includes/doctor.php");
require_once(dirname(__FILE__) . "/includes/BasicProfile.php");
require_once(dirname(__FILE__) . "/includes/functions.php");
//$basicProfile=BasicProfile::find_by_docid($_GET['docid']);

if (!empty($basicProfile)) {
    redirect_to("viewProfile.php?docid=$basicProfile->docid");
}
$pageTitle = "Add Basic Profile";
$empName = Employee::find_by_empid($_SESSION['employee']);

$errors2 = array();
if (isset($_GET['docid'])) {
    //echo $_POST['docid'];
    $doctorName = Doctor::find_by_docid($_GET['docid']);
} else {
    redirect_to("AddProfile.php");
}
$count = BasicProfile::count_all($doctorName->empid);



if (isset($_POST['submit1'])) {
    $newBasicProfile = new BasicProfile();
    $newBasicProfile->docid = $doctorName->docid;
    $newBasicProfile->name = $doctorName->name;
    $newBasicProfile->empid = $doctorName->empid;
    $newBasicProfile->id = ++$count;
    if (isset($_POST['DOB']) && $_POST['DOB'] != '') {
        $newBasicProfile->DOB = date("Y-m-d", strtotime($_POST['DOB']));
    } else {
        $newBasicProfile->DOB = '0000-00-00';
    }

    if (isset($_POST['DOA']) && $_POST['DOA'] != '') {
        $newBasicProfile->DOA = date("Y-m-d", strtotime($_POST['DOA']));
    } else {
        $newBasicProfile->DOA = '0000-00-00';
    }

    $fields = array('clinic_address', 'residential_address',
        'receive_mailers', 'receive_sms', 'yrs_of_practice',
        'inclination_to_speaker', 'potential_to_speaker',
        'other', 'total', 'gen_ophthal', 'retina', 'glaucoma', 'cornea', 'any_other',
        'plot1', 'street1', 'area1', 'state1', 'pincode1', 'plot2', 'street2', 'area2', 'state2', 'pincode2', 'city1', 'city2',
        'daily_opd', 'value_per_rx', 'value_per_month', 'pharma_potential', 'msl_code', 'clinic_name', 'class');
    foreach ($fields as $item) {
        if (isset($_POST[$item])) {
            $newBasicProfile->{$item} = trim($_POST[$item]);
        }
    }

    $newBasicProfile->month = trim($_POST['months']);

    if ($_POST['behaviour'] === 'Any other please specify') {
        $newBasicProfile->behaviour = $_POST['behaviour1'];
    } else {
        $newBasicProfile->behaviour = $_POST['behaviour'];
    }


    if (!empty($_POST['Hobbies'])) {
        $hobbie = implode(',', $_POST['Hobbies']);
        $newBasicProfile->hobbies = $hobbie;
    }


    if (!empty($_POST['activity_inclination'])) {
        $activity = implode(',', $_POST['activity_inclination']);
        $newBasicProfile->activity_inclination = $activity;
    }


    if (!empty($_POST['type'])) {
        $newBasicProfile->type = implode(",", $_POST['type']);
    } else {
        $newBasicProfile->type = 'NA';
    }

    if (empty($errors2)) {
        $rowCount = BasicProfile::find_by_docid($newBasicProfile->docid);
        if (empty($rowCount)) {
            if ($newBasicProfile->create()) {

                // Success
                $message = "Added Succesfully.";
                redirect_to('AddProfile.php');
            } else {
                // Failure
                echo "failed";
            }
        } else {
            array_push($errors2, "Doctor details already exist");
        }
    }
}
require_once("layouts/TMheader.php");
?>

<link href="css/bootstrap-multiselect.css" rel="stylesheet">


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Basic Profile<small><?php echo "  " . $doctorName->name ?></small></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <ul>
        <?php foreach ($errors2 as $val) { ?>
            <li style="color:red"><?php echo $val; ?></li>
        <?php } ?>
    </ul>
</div>
<style>
    .address  {
        margin-top: 2px;
        width : 100%;
    }
</style>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 ">
        <form action="basicProfiles.php?docid=<?php echo $doctorName->docid; ?>" method="post" id="form1">  
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12 ">
                    <label>Date Of Birth</label>
                    <input type="text" name="DOB"  value="<?php
                    if (isset($_POST['DOB'])) {
                        echo $_POST['DOB'];
                    }
                    ?>" id="datepicker" autocomplete="off" class="form-control" style="" />
                </div>
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12 ">
                    <label>Date Of Aniversary</label>
                    <input type="text" name="DOA" value="<?php
                    if (isset($_POST['DOB'])) {
                        echo $_POST['DOA'];
                    }
                    ?>" id="datepicker1" autocomplete="off"  class="form-control" style="" />
                </div>
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12 form-group">
                    <label>Clinic Name</label>
                    <input type="text" name="clinic_name"  value=""  class="form-control clinic" />
                </div>
            </div>
            <div class="row"  style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 ">
                    <label>Class</label>
                    <div class="row">
                        <div class="col-lg-6 col-xs-4">
                            <select name="class" class="form-control " >
                                <option value>Select Class</option>
                                <option value='A++' <?php
                                if (isset($_POST['class']) && $_POST['class'] == 'A++') {
                                    echo "selected";
                                }
                                ?> >A++
                                </option> 
                                <option value='A+' <?php
                                if (isset($_POST['class']) && $_POST['class'] == 'A+') {
                                    echo "selected";
                                }
                                ?>>A+
                                </option>
                                <option value='A' <?php
                                if (isset($_POST['class']) && $_POST['class'] == 'A') {
                                    echo "selected";
                                }
                                ?>>A</option>
                                <option value='B' <?php
                                if (isset($_POST['class']) && $_POST['class'] == 'B') {
                                    echo "selected";
                                }
                                ?>>B</option>

                                <option value='Z' <?php
                                if (isset($_POST['class']) && $_POST['class'] == 'Z') {
                                    echo "selected";
                                }
                                ?>>Z
                                </option>
                            </select>
                        </div>
                        <div class="btn-group col-lg-1 col-xs-1" style="padding-top:4px;padding-left: 0px">
                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-info-circle"></i>
                            </button>
                            <ul class="dropdown-menu" role="menu" style="padding:7px">
                                <li><b>A++ Doctor :</b> Potential more than 2.5 Lakhs/Cipla business more than 25000/KOL/Competitor Dr</li>
                                <li class="divider"></li>
                                <li><b>A+ Doctor:</b> Potential more than 1.5 lakhs/ Cipla business 10000-25000/KOL may be or may not</li>
                                <li class="divider"></li>
                                <li><b>A Doctor:</b> Senior Resident/GPs/Optometrist/ENT/Potential between 1 lakh – 1.5 lakhs/Cipla business between 5000-10000/ 0-5 years of practice/Young or Upcoming Dr</li>
                                <li class="divider"></li>
                                <li><b>B Class:</b> Potential upto 1 lakh/ Cipla business between 0 – 5000</li>
                                <li class="divider"></li>
                                <li><b>Z Class:</b> All Resident/PG doctors</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 form-group">
                    <label>Years Of Practice</label>
                    <div class="row">
                        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">					
                            <input type="text" name="yrs_of_practice" maxlength="30" value="<?php
                            if (isset($_POST['yrs_of_practice'])) {
                                echo $_POST['yrs_of_practice'];
                            }
                            ?>"  placeholder="Years" class="form-control customWidth1" />
                        </div>
                        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">	
                            <input type="text" name="months" maxlength="" value="<?php
                            if (isset($_POST['months'])) {
                                echo $_POST['months'];
                            }
                            ?>" placeholder="Months"  class="form-control customWidth1" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row"  style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 form-group">
                    <label>Want to recieve mailers</label>
                    <div class="radio">
                        <label><input type="radio" name="receive_mailers" value="yes" <?php
                            if (isset($_POST['receive_mailers']) && $_POST['receive_mailers'] == 'yes') {
                                echo "checked";
                            }
                            ?> > Yes </label>
                        <label><input type="radio" name="receive_mailers" value="no" <?php
                            if (isset($_POST['receive_mailers']) && $_POST['receive_mailers'] == 'no') {
                                echo "checked";
                            }
                            ?>> No  </label>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 form-group">
                    <label>Want to recieve SMS</label>
                    <div class="radio">
                        <label><input type="radio" name="receive_sms" value="yes" <?php
                            if (isset($_POST['receive_sms']) && $_POST['receive_sms'] == 'yes') {
                                echo "checked";
                            }
                            ?> > Yes</label>
                        <label><input type="radio" name="receive_sms" value="no" <?php
                            if (isset($_POST['receive_sms']) && $_POST['receive_sms'] == 'no') {
                                echo "checked";
                            }
                            ?> > No</label>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Type Of Doctor</label><br/>
                    <select  class="form-control multiselect " name="type[]" multiple="multiple" >
                        <option value='Dispensing with attached Chemist'  >Dispensing with attached Chemist</option>
                        <option value='Dispensing without attached Chemist'  >Dispensing without attached Chemist</option>
                        <option value='Attached to an Institute' >Attached to an Institute</option>
                        <option value='Attached to a corporate hospital' >Attached to a corporate hospital</option>
                        <option value='Private Practitioner'  >Private Practitioner</option>
                        <option value='Group Practice for Lasik' >Group Practice for Lasik</option>
                    </select> 
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Behaviour Of Doctor</label>

                    <select name="behaviour" class="form-control customWidth" style="width:55%" id="specifyCheck3">
                        <option value>Select Behaviour</option>
                        <option value='Friendly' <?php
                        if (isset($_POST['behaviour'])) {
                            foreach ($_POST['behaviour'] as $behaviour) {
                                if ($behaviour == 'Friendly') {
                                    echo "selected";
                                }
                            }
                        }
                        ?> >Friendly</option>
                        <option value='Professional' <?php
                        if (isset($_POST['behaviour'])) {
                            foreach ($_POST['behaviour'] as $behaviour) {
                                if ($behaviour == 'Professional') {
                                    echo "selected";
                                }
                            }
                        }
                        ?>>Professional</option>
                        <option value='Non responsive to in clinic activity'  <?php
                        if (isset($_POST['behaviour'])) {
                            foreach ($_POST['behaviour'] as $behaviour) {
                                if ($behaviour == 'Non responsive to in clinic activity') {
                                    echo "selected";
                                }
                            }
                        }
                        ?>>Non responsive to in clinic activity</option>
                        <option  value='Any other please specify' <?php
                        if (isset($_POST['behaviour'])) {
                            foreach ($_POST['behaviour'] as $behaviour) {
                                if ($behaviour == 'Any other please specify') {
                                    echo "selected";
                                }
                            }
                        }
                        ?> >Any other please specify</option>
                    </select> 
                    <input type="text" name="behaviour2" maxlength="30" value="" id="specify"  class="form-control" style="width:50%"/>
                </div>

            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 ">
                    <label>Inclination To Speaker</label>
                    <div class="radio">
                        <label><input type="radio" name="inclination_to_speaker" value="yes" <?php
                            if (isset($_POST['inclination_to_speaker']) && $_POST['inclination_to_speaker'] == 'yes') {
                                echo "checked";
                            }
                            ?>> Yes</label>
                        <label><input type="radio" name="inclination_to_speaker" value="no"  <?php
                            if (isset($_POST['inclination_to_speaker']) && $_POST['inclination_to_speaker'] == 'no') {
                                echo "checked";
                            }
                            ?> > No</label>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12 ">
                    <label>Potential To Speaker</label>
                    <div class="radio">
                        <label><input type="radio" name="potential_to_speaker" value="yes"  <?php
                            if (isset($_POST['potential_to_speaker']) && $_POST['potential_to_speaker'] == 'yes') {
                                echo "checked";
                            }
                            ?> > Yes</label>
                        <label><input type="radio" name="potential_to_speaker" value="no" <?php
                            if (isset($_POST['potential_to_speaker']) && $_POST['potential_to_speaker'] == 'no') {
                                echo "checked";
                            }
                            ?> > No</label>
                    </div>
                </div>
            </div>
            <div class="row " style="margin-bottom:1em;">
                <div class="col-lg-6 col-xs-12" id="no-more-tables">
                    <table class="table table-bordered">
                        
                        <tr>
                            <th class="hidden-xs"></th>
                            <th>Clinic Address</th>
                        </tr>
                        <tr>
                            <td class="hidden-xs" ><label>Plot No</label></td>
                            <td data-title="Plot No"><input type="text" name="plot1" class="form-control address" placeholder="Plot No" >
                            </td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>Street No/Road No</label></td>
                            <td data-title="Street No/Road No"> <input type="text" name="street1" class="form-control address" placeholder="Street No/Road No" >
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>Area</label></td>
                            <td data-title="Area"><input type="text" name="area1" class="form-control address" placeholder="Area"  ></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>City</label></td>
                            <td data-title="City"><input type="text" name="city1" class="form-control address" placeholder="City"  ></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>State</label></td>
                            <td data-title="State"><select class="form-control address" name="state1">
                                    <?php echo stateList(); ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>Pin Code</label>
                            </td>
                            <td data-title="Pincode"><input type="text" name="pincode1" class="form-control address" placeholder="Pin Code"  >                </td>
                        </tr>
                    </table>

                </div>
                <div class="col-lg-6 col-xs-12" id="no-more-tables">

                    <table class="table table-bordered">
                        <tr>
                            <th>Residential Address</th>
                        </tr>
                        <tr>
                            <td data-title="Plot No"><input type="text" name="plot2" class="form-control address" placeholder="Plot No" >
                            </td>
                        </tr>
                        <tr>
                            <td data-title="Street No/Road No"><input type="text" name="street2" class="form-control address" placeholder="Street No/Road No" ></td>
                        </tr>
                        <tr>
                            <td data-title="Area"><input type="text" name="area2" class="form-control address" placeholder="Area" ></td>
                        </tr>
                        <tr>
                            <td data-title="City"><input type="text" name="city2" class="form-control address" placeholder="City" ></td>
                        </tr>
                        <tr>
                            <td data-title="State"><select class="form-control address" name="state2">
                                    <?php echo stateList(); ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td data-title="Pincode"><input type="text" name="pincode2" class="form-control address" placeholder="Pin Code" ></td>
                        </tr>
                    </table>

                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Hobbies And Interest</label>
                    <div class="checkbox">
                        <label>	<input type="checkbox" name="Hobbies[]" value="Sports" />Sports</label><br/>
                        <label> <input type="checkbox" name="Hobbies[]" value="Music "  / >Music</label><br/>
                        <label> <input type="checkbox" name="Hobbies[]" value="Photography"  />Photography</label><br/>
                        <label> <input type="checkbox" name="Hobbies[]" value="Internet/ Tech Savy"  />Internet/ Tech Savy</label><br/>
                        <label><input type="checkbox" name="Hobbies[]" id="specifyCheck" value="" />Any other specific activity(Pls Specify)</label><br/>
                        <label><input type="text" name="Hobbies[]" maxlength="30" value="" id="specify1" style="display:none"  class="form-control" style="width:50%"/>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Activity Inclination</label>
                    <div class="checkbox">
                        <label><input type="checkbox" name="activity_inclination[]" value="Patient Education Material" <?php
                            if (isset($_POST['activity_inclination'])) {
                                foreach ($_POST['activity_inclination'] as $activity) {
                                    if ($activity == 'Patient Education Material') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?>
                                      />Patient Education Material</label><br/>
                        <label><input type="checkbox" name="activity_inclination[]" value="Patient Awareness Camps"    <?php
                            if (isset($_POST['activity_inclination'])) {
                                foreach ($_POST['activity_inclination'] as $activity) {
                                    if ($activity == 'Disease Awareness Camps') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?>
                                      />Patient Awareness Camps</label><br/>

                        <label><input type="checkbox" name="activity_inclination[]" value="Paramedics training sessions" <?php
                            if (isset($_POST['activity_inclination'])) {
                                foreach ($_POST['activity_inclination'] as $activity) {
                                    if ($activity == 'Paramedics training sessions') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?> />Paramedics training sessions</label><br/>
                        <label><input type="checkbox" name="activity_inclination[]" id="specifyCheck1" value=""  />Workshops if Any ( Pls Specify)</label><br/>
                        <label><input type="text" name="activity_inclination[]" maxlength="30" value=""   id="specify2" style="display:none"  class="form-control" style="width:50%" />
                    </div>
                </div>

            </div>
            <div class="row"><div class="col-lg-12"><label>Type Of Practice</label></div></div>
            <hr/>
            <div class="row" style="margin-bottom:1em;" id="items">
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 form-group ">
                    <label>Gen Opthal</label>
                    <div class="input-group customWidth">
                        <input type="text" name="gen_ophthal" maxlength="30" value="<?php
                        if (isset($_POST['gen_ophthal'])) {
                            echo $_POST['gen_ophthal'];
                        }
                        ?>" class="form-control common"
                               id="id1"/>
                        <span class="input-group-addon">%</span>
                    </div>	
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 form-group">
                    <label>Retina</label>
                    <div class=" input-group customWidth">
                        <input type="text" name="retina" maxlength="30" value="<?php
                        if (isset($_POST['retina'])) {
                            echo $_POST['retina'];
                        }
                        ?>"  class="form-control common"
                               id="id2"/>
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 form-group">.
                    <label>Glaucoma</label>
                    <div class=" input-group customWidth">
                        <input type="text" name="glaucoma" maxlength="30" value="<?php
                        if (isset($_POST['glaucoma'])) {
                            echo $_POST['glaucoma'];
                        }
                        ?>" class="form-control common"
                               id="id3" /> 
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 form-group">
                    <label>Cornea</label>
                    <div class=" input-group customWidth">
                        <input type="text" name="cornea" maxlength="30" value="<?php
                        if (isset($_POST['cornea'])) {
                            echo $_POST['cornea'];
                        }
                        ?>"   class="form-control common"
                               id="id4"/>
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 form-group">
                    <label>Any Other</label>
                    <input class="form-control" name="any_other" placeholder="Specify Here" style="margin-bottom: 5px">
                    <div class=" input-group customWidth">
                        <input type="text" name="other" maxlength="30" value="<?php
                        if (isset($_POST['other'])) {
                            echo $_POST['other'];
                        }
                        ?>"   class="form-control common"
                               id="id5"/>
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 form-group">
                    <label>Total</label>
                    <div class=" input-group customWidth">
                        <input type="text" name="total" maxlength="30"  readonly id="id6" class="form-control " />
                        <span class="input-group-addon">%</span>
                    </div>
                    <div class="row">
                        <input type="hidden" name="finaltotal" value="100" id="finaltotal">
                        <input type="text" readonly  id="warning"  style="color:red;border-style:none;font-weight:bold"/>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Average Daily OPD </label>
                    <input type="text" name="daily_opd" maxlength="30" value="<?php
                    if (isset($_POST['daily_opd'])) {
                        echo $_POST['daily_opd'];
                    }
                    ?>"  class="form-control customWidth" placeholder="No. of Patients"/>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Average Value Per Rx </label>
                    <input type="text" name="value_per_rx" maxlength="30" value="<?php
                    if (isset($_POST['value_per_rx'])) {
                        echo $_POST['value_per_rx'];
                    }
                    ?>"  class="form-control customWidth" placeholder="Enter value in Rs."  />
                </div>
                <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12 ">
                    <label>Average Surgery Per Month </label>
                    <input type="text" name="value_per_month" maxlength="30" value="<?php
                    if (isset($_POST['value_per_month'])) {
                        echo $_POST['value_per_month'];
                    }
                    ?>"  class="form-control customWidth" placeholder="Enter No. of surgeries"  />
                </div>
                <div class="col-lg-3 col-sm-3 col-md-3s col-xs-12 ">
                    <label>Pharma Potential </label>
                    <input type="text" name="pharma_potential" maxlength="30" value="<?php
                    if (isset($_POST['pharma_potential'])) {
                        echo $_POST['pharma_potential'];
                    }
                    ?>" class="form-control customWidth" placeholder="Enter value in Rs."  />
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>MSL Code</label>
                    <input type="text" name="msl_code" maxlength="30" value="<?php
                    if (isset($_POST['msl_code'])) {
                        echo $_POST['msl_code'];
                    }
                    ?>" class="form-control customWidth"   />
                </div>				
            </div>
            <hr/>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12 ">
                    <input type="submit" name="submit1" value="Save" class="btn btn-primary " onclick="return validate()" id="save" />
                </div>
            </div>
        </form>
    </div>
</div>
</div>

</div>
<!--

 Metis Menu Plugin JavaScript 
<script src="js/plugins/metisMenu/metisMenu.min.js"></script>

 Custom Theme JavaScript 
<script src="js/sb-admin-2.js"></script>-->

<script>
    $(document).ready(function () {
        jQuery("#items").delegate('.common ', 'keyup', function () {

            if ($("#id1").val().length == 0)
                $("#id1").val('0');

            if ($("#id2").val().length == 0)
                $("#id2").val('0');

            if ($("#id3").val().length == 0)
                $("#id3").val('0');

            if ($("#id4").val().length == 0)
                $("#id4").val('0');

            if ($("#id5").val().length == 0)
                $("#id5").val('0');

            var val1 = $("#id1").val();
            var val2 = $("#id2").val();
            var val3 = $("#id3").val();
            var val4 = $("#id4").val();
            var val5 = $("#id5").val();

            var totalCount = parseInt(val1, 10) + parseInt(val2, 10) + parseInt(val3, 10)
                    + parseInt(val4, 10) + parseInt(val5, 10);
            $("#id6").val(totalCount);


            if (totalCount < 100 || totalCount > 100) {
                $("#warning").val('Total has to be 100');
                $("#save").hide();
            } else {
                $("#warning").val('');
                $("#save").show();
            }


        });

    });
</script>
<script>
    function validate() {
        var total = $("#id6").val();

        if (total !== '100') {
            alert("Total Has to be hundred");
            return false;
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#specify").css("display", "none");
        $("#specify1").css("display", "none");
        $("#specify2").css("display", "none");

        $("#specifyCheck").click(function () {

            $("#specify1").toggle();
        });

        $("#specifyCheck1").click(function () {
            $("#specify2").toggle();
        });

        $('#specifyCheck3').change(function () {
            if ($(this).val() == 'Any other please specify') {
                $('#specify').css({'display': 'block'});
            } else {
                $('#specify').css({'display': 'none'});
            }
        });


        $('.clinic').change(function () {
            var value = $(this).val();
            $('.clinic1').val(value);

        });
    });
</script>
<?php require_once("layouts/TMfooter.php"); ?>
<script >
    $(document).ready(function () {

        $('.multiselect').multiselect({
            numberDisplayed: 1
        });

    });
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />

<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

<style type="text/css">

    #dateRangeForm .form-control-feedback {
        top: 0;
        right: -15px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $('#datepicker').datepicker({format: 'yyyy-mm-dd', }).on('changeDate', function (e) {
            // Revalidate the date field
            $('#form1').formValidation('revalidateField', 'DOB');
        });
        $('#datepicker1').datepicker({format: 'yyyy-mm-dd', }).on('changeDate', function (e) {
            // Revalidate the date field
            $('#form1').formValidation('revalidateField', 'DOA');
        });

        $('#form1').formValidation({
            message: 'This value is not valid',
            icon: {
            },
            fields: {
                value_per_rx: {
                    validators: {
                        digits: {
                            message: 'Value per rx has to be in number'
                        }
                    }
                },
                pharma_potential: {
                    validators: {
                        digits: {
                            message: 'Pharma Potential has to be in number'
                        }
                    }
                },
                yrs_of_practice: {
                    validators: {
                        digits: {
                            message: 'This has to be in number'
                        }
                    }
                },
                months: {
                    validators: {
                        digits: {
                            message: 'This has to be in number'
                        }
                    }
                },
                gen_ophthal: {
                    validators: {
                        digits: {
                            message: 'This has to be in number'
                        }
                    }
                },
                retina: {
                    validators: {
                        digits: {
                            message: 'This has to be in number'
                        }
                    }
                },
                cornea: {
                    validators: {
                        digits: {
                            message: 'This has to be in number'
                        }
                    }
                },
                glaucoma: {
                    validators: {
                        digits: {
                            message: 'This has to be in number'
                        }
                    }
                },
            }
        });
    });
</script>
</body>
</html>