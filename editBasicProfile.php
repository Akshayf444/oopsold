<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");

$errors2 = array();
if (isset($_GET['docid'])) {
    $doctorName = Doctor::find_by_docid($_GET['docid']);
    $basicProfile = BasicProfile::find_by_docid($_GET['docid']);
} else {
    redirect_to("AddProfile.php");
}

$page = $_GET['page'];

if (isset($_POST['submit1'])) {
    $newBasicProfile = new BasicProfile();
    $newBasicProfile->docid = $_GET['docid'];
    $newBasicProfile->name = $doctorName->name;
    $newBasicProfile->empid = $basicProfile->empid;
    $newBasicProfile->id = $basicProfile->id;
    //Date conversion
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

    $fields = array('clinic_address', 'residential_address',
        'receive_mailers', 'receive_sms', 'yrs_of_practice',
        'inclination_to_speaker', 'potential_to_speaker',
        'other', 'total', 'gen_ophthal', 'retina', 'glaucoma', 'cornea', 'any_other',
        'plot1', 'street1', 'area1', 'state1', 'pincode1', 'plot2', 'street2', 'area2', 'state2', 'pincode2', 'city1', 'city2',
        'daily_opd', 'value_per_rx', 'value_per_month', 'pharma_potential', 'msl_code', 'clinic_name', 'class');

    foreach ($fields as $item) {
        //echo $item;
        if (isset($_POST[$item])) {
            $newBasicProfile->{$item} = trim($_POST[$item]);
        }
    }

    if (empty($errors2)) {
        if ($newBasicProfile->update($_GET['docid'])) {
            redirect_to("viewProfile.php?docid=$basicProfile->docid&page={$page}");
        }
    }
}
$pageTitle = "Edit Basic Profile";
require_once("layouts/TMheader.php");
?>
<link href="css/bootstrap-multiselect.css" rel="stylesheet">
<script src="js/bootstrap-multiselect.js"></script>
<!-- Include Bootstrap Datepicker -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />

<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

<style type="text/css">

    #dateRangeForm .form-control-feedback {
        top: 0;
        right: -15px;
    }
</style>
<script language="javascript" type="text/javascript">
    function funCalculateTotal(id1, id2, id3, id4, id5, id6) {

        var totalCount = parseInt(val1, 10) + parseInt(val2, 10) + parseInt(val3, 10)
                + parseInt(val4, 10) + parseInt(val5, 10);
        $("#" + id6).val(totalCount);
        if (totalCount < 100 || totalCount > 100) {
            $("#id6").val('Total has to be 100');
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
<script type="text/javascript">
    $(document).ready(function () {

        $('.multiselect').multiselect({
            numberDisplayed: 1
        });

    });
</script>
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
            } else {
                $("#warning").val('');
            }
        });
    });
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Edit Basic Profile<small><?php echo "  " . $doctorName->name ?></small></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<style>
    .address  {
        margin-top: 2px;
        width: 100%;
    }
</style>
<div class="row">
    <ul>
        <?php foreach ($errors2 as $val) { ?>
            <li style="color:red;list-style-type:none"><?php echo $val; ?></li>
        <?php } ?>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <form action="editBasicProfile.php?docid=<?php echo $basicProfile->docid; ?>&page=<?php echo $page; ?>" method="post" id="form1">  
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <label>Date Of Birth</label>
                    <input type="text" name="DOB"  value="<?php
                    if (isset($_POST['DOB'])) {
                        echo $_POST['DOB'];
                    } else {
                        if ($basicProfile->DOB == '0000-00-00') {
                            echo '';
                        } else {
                            echo $basicProfile->DOB;
                        }
                    }
                    ?>" id="datepicker" autocomplete="off" class="form-control"  />
                </div>
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <label>Date Of Aniversary</label>
                    <input type="text" name="DOA" value="<?php
                    if (isset($_POST['DOA'])) {
                        echo $_POST['DOA'];
                    } else {
                        if ($basicProfile->DOA == '0000-00-00') {
                            echo '';
                        } else {
                            echo $basicProfile->DOA;
                        }
                    }
                    ?>" id="datepicker1" autocomplete="off"  class="form-control"  />
                </div>
                <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
                    <label>Clinic Name</label>
                    <input type="text" name="clinic_name"  value="<?php echo $basicProfile->clinic_name; ?>"  class="form-control clinic" />
                </div>
            </div>
            <div class="row"  style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Class</label>
                    <div class="row">
                        <div class="col-lg-6 col-xs-3">
                            <select name="class" class="form-control " >
                                <option value>Select Class</option>
                                <option value='A++' <?php
                                if ($basicProfile->class == 'A++') {
                                    echo "selected";
                                }
                                ?> >A++</option> 
                                <option value='A+' <?php
                                if ($basicProfile->class == 'A+') {
                                    echo "selected";
                                }
                                ?>>A+</option>
                                <option value='A' <?php
                                if ($basicProfile->class == 'A') {
                                    echo "selected";
                                }
                                ?>>A</option>
                                <option value='B' <?php
                                if ($basicProfile->class == 'B') {
                                    echo "selected";
                                }
                                ?>>B</option>

                                <option value='Z' <?php
                                if ($basicProfile->class == 'Z') {
                                    echo "selected";
                                }
                                ?>>Z</option>
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
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Years Of Practice</label>
                    <div class="row">
                        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">					
                            <input type="text" name="yrs_of_practice" maxlength="30" value="<?php
                            echo $basicProfile->yrs_of_practice;
                            ?>"  placeholder="Years" class="form-control customWidth1" />
                        </div>
                        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">	
                            <input type="text" name="months" maxlength="" value="<?php
                            echo $basicProfile->month;
                            ?>" placeholder="Months"  class="form-control customWidth1" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row"  style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Want to receive mailers</label>
                    <div class="radio">
                        <label><input type="radio" name="receive_mailers" value="yes" <?php
                            if ($basicProfile->receive_mailers == 'yes') {
                                echo "checked";
                            }
                            ?>> Yes </label>
                        <label><input type="radio" name="receive_mailers" value="no" <?php
                            if ($basicProfile->receive_mailers == 'no') {
                                echo "checked";
                            }
                            ?> > No  </label>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Want to receive SMS</label>
                    <div class="radio">
                        <label><input type="radio" name="receive_sms" value="yes" <?php
                            if ($basicProfile->receive_sms == 'yes') {
                                echo "checked";
                            }
                            ?> > Yes</label>
                        <label><input type="radio" name="receive_sms" value="no" <?php
                            if ($basicProfile->receive_sms == 'no') {
                                echo "checked";
                            }
                            ?> > No</label>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Type Of Doctor</label><br/>
                    <select  class="form-control multiselect customWidth" name="type[]" multiple="multiple" >
                        <?php $data = explode(",", $basicProfile->type); ?>
                        <option value='Dispensing with attached Chemist' <?php
                        if (in_array('Dispensing with attached Chemist', $data)) {
                            echo 'selected';
                        }
                        ?>>Dispensing with attached Chemist</option>
                        <option value='Dispensing without attached Chemist' <?php
                        if (in_array('Dispensing without attached Chemist', $data)) {
                            echo 'selected';
                        }
                        ?>>Dispensing without attached Chemist</option>
                        <option value='Attached to an Institute' <?php
                        if (in_array('Attached to an Institute', $data)) {
                            echo 'selected';
                        }
                        ?>>Attached to an Institute</option>
                        <option value='Attached to a corporate hospital' <?php
                        if (in_array('Attached to a corporate hospital', $data)) {
                            echo 'selected';
                        }
                        ?>>Attached to a corporate hospital</option>
                        <option value='Private Practitioner' <?php
                        if (in_array('Private Practitioner', $data)) {
                            echo 'selected';
                        }
                        ?>>Private Practitioner</option>
                        <option value='Group Practice for Lasik' <?php
                        if (in_array('Group Practice for Lasik', $data)) {
                            echo 'selected';
                        }
                        ?>>Group Practice for Lasik</option>
                    </select>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Behaviour Of Doctor</label>
                    <select name="behaviour" class="form-control customWidth" id="specifyCheck3" style="width:55%">

                        <option value >Select Behaviour</option>

                        <option value='Friendly' <?php
                        if ($basicProfile->behaviour == 'Friendly') {
                            echo "selected";
                        }
                        ?> >Friendly</option>

                        <option value='Professional' <?php
                        if ($basicProfile->behaviour == 'Professional') {
                            echo "selected";
                        }
                        ?>>Professional</option>

                        <option value='Non responsive to in clinic activity' <?php
                        if ($basicProfile->behaviour == 'Non responsive to in clinic activity') {
                            echo "selected";
                        }
                        ?> >Non responsive to in clinic activity</option>

                        <option  value='Any other please specify' <?php
                        if ($basicProfile->behaviour == 'Any other please specify') {
                            echo "selected";
                        }
                        ?>>Any other please specify</option>

                    </select> 
                    <input type="text" name="behaviour1" maxlength="30" value="" id="specify"  class="form-control" style="width:50%"/>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Inclination To Speaker</label>
                    <div class="radio">
                        <label><input type="radio" name="inclination_to_speaker" value="yes" <?php
                            if (isset($_POST['inclination_to_speaker']) && $_POST['inclination_to_speaker'] == 'yes') {
                                echo "checked";
                            } else {
                                if ($basicProfile->inclination_to_speaker == 'yes') {
                                    echo "checked";
                                }
                            }
                            ?> > Yes</label>

                        <label><input type="radio" name="inclination_to_speaker" value="no" <?php
                            if (isset($_POST['inclination_to_speaker']) && $_POST['inclination_to_speaker'] == 'no') {
                                echo "checked";
                            } else {
                                if ($basicProfile->inclination_to_speaker == 'no') {
                                    echo "checked";
                                }
                            }
                            ?> > No</label>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Potential To Speaker</label>
                    <div class="radio">
                        <label><input type="radio" name="potential_to_speaker" value="yes" <?php
                            if (isset($_POST['potential_to_speaker']) && $_POST['potential_to_speaker'] == 'yes') {
                                echo "checked";
                            } else {
                                if ($basicProfile->potential_to_speaker == 'yes') {
                                    echo "checked";
                                }
                            }
                            ?> > Yes</label>
                        <label><input type="radio" name="potential_to_speaker" value="no" <?php
                            if (isset($_POST['potential_to_speaker']) && $_POST['potential_to_speaker'] == 'no') {
                                echo "checked";
                            } else {
                                if ($basicProfile->potential_to_speaker == 'no') {
                                    echo "checked";
                                }
                            }
                            ?>  > No</label>
                    </div>
                </div>
            </div>
            <div class="row " style="margin-bottom:1em;">
                <div class="col-lg-6 col-xs-12" id="no-more-tables" >
                    <table class="table table-bordered">
                        <tr>
                            <th class="hidden-xs"></th>
                            <th>Clinic Address</th>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>Plot No</label></td>
                            <td data-title="Plot No"><input type="text" name="plot1" class="form-control address" placeholder="Plot No" value="<?php echo $basicProfile->plot1; ?>"></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>Street No/Road No</label></td>
                            <td data-title="Street No/Road No"> <input type="text" name="street1" class="form-control address" placeholder="Street No/Road No" value="<?php echo $basicProfile->street1; ?>"></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>Area</label></td>
                            <td data-title="Area"><input type="text" name="area1" class="form-control address" placeholder="Area"  value="<?php echo $basicProfile->area1; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>City</label></td>
                            <td data-title="City"><input type="text" name="city1" class="form-control address" placeholder="City"  value="<?php echo $basicProfile->city1; ?>"> </td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>State</label> </td>
                            <td data-title="State"><select class="form-control address" name="state1"><?php echo stateList($basicProfile->state1); ?></select></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><label>Pin Code</label></td>
                            <td data-title="Pin Code"><input type="text" name="pincode1" class="form-control address" placeholder="Pin Code"  value="<?php echo $basicProfile->pincode1; ?>"></td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6 col-xs-12" id="no-more-tables" >
                    <table class="table table-bordered">
                        <tr>
                            <th>Residential Address</th>
                        </tr>
                        <tr>
                            <td data-title="Plot No"><input type="text" name="plot2" class="form-control address" placeholder="Plot No" value="<?php echo $basicProfile->plot2; ?>"></td>
                        </tr>
                        <tr>
                            <td data-title="Street No"><input type="text" name="street2" class="form-control address" placeholder="Street No/Road No" value="<?php echo $basicProfile->street2; ?>"></td>
                        </tr>
                        <tr>
                            <td data-title="Area"><input type="text" name="area2" class="form-control address" placeholder="Area" value="<?php echo $basicProfile->area2; ?>"></td>
                        </tr>
                        <tr>
                            <td data-title="City"><input type="text" name="city2" class="form-control address" placeholder="City" value="<?php echo $basicProfile->city2; ?>"></td>
                        </tr>
                        <tr>
                            <td data-title="State"><select class="form-control address" name="state2"><?php echo stateList($basicProfile->state2); ?> </select></td>
                        </tr>
                        <tr>
                            <td data-title="Pin Code"><input type="text" name="pincode2" class="form-control address" placeholder="Pin Code" value="<?php echo $basicProfile->pincode2; ?>"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Hobbies And Interest</label>
                    <div class="checkbox">
                        <label>	<input type="checkbox" name="Hobbies[]" <?php
                            if (isset($_POST['Hobbies'])) {
                                foreach ($_POST['Hobbies'] as $hobbie) {
                                    if ($hobbie == 'Sports') {
                                        echo "checked";
                                    }
                                }
                            } else {
                                $hobbies = explode(",", $basicProfile->hobbies);
                                foreach ($hobbies as $hobbie) {
                                    if ($hobbie == 'Sports') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?>  value="Sports" />Sports</label><br/>
                        <label> <input type="checkbox" name="Hobbies[]" value="Music " <?php
                            if (isset($_POST['Hobbies'])) {
                                foreach ($_POST['Hobbies'] as $hobbie) {
                                    if ($hobbie == 'Music ') {
                                        echo "checked";
                                    }
                                }
                            } else {
                                $hobbies = explode(",", $basicProfile->hobbies);
                                foreach ($hobbies as $hobbie) {
                                    if ($hobbie == 'Music ') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?> / >Music</label><br/>
                        <label> <input type="checkbox" name="Hobbies[]" value="Photography" <?php
                            if (isset($_POST['Hobbies'])) {
                                foreach ($_POST['Hobbies'] as $hobbie) {
                                    if ($hobbie == 'Photography') {
                                        echo "checked";
                                    }
                                }
                            } else {
                                $hobbies = explode(",", $basicProfile->hobbies);
                                foreach ($hobbies as $hobbie) {
                                    if ($hobbie == 'Photography') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?> />Photography</label><br/>
                        <label> <input type="checkbox" name="Hobbies[]" value="Internet/ Tech Savy" <?php
                            if (isset($_POST['Hobbies'])) {
                                foreach ($_POST['Hobbies'] as $hobbie) {
                                    if ($hobbie == 'Internet/ Tech Savy') {
                                        echo "checked";
                                    }
                                }
                            } else {
                                $hobbies = explode(",", $basicProfile->hobbies);
                                foreach ($hobbies as $hobbie) {
                                    if ($hobbie == 'Internet/ Tech Savy') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?> />Internet/ Tech Savy</label><br/>
                        <label><input type="checkbox" name="Hobbies[]" id="specifyCheck" value=""  />Any other specific activity(Pls Specify)</label><br/>
                        <label><input type="text" name="Hobbies[]" maxlength="30" value="" id="specify1" style="display:none"  class="form-control" style="width:50%"/>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Activity Inclination</label>
                    <div class="checkbox">
                        <label><input type="checkbox" name="activity_inclination[]" value="Patient Education Material" <?php
                            if (isset($_POST['activity_inclination'])) {
                                foreach ($_POST['activity_inclination'] as $hobbie) {
                                    if ($hobbie == 'Patient Education Material') {
                                        echo "checked";
                                    }
                                }
                            } else {
                                $hobbies = explode(",", $basicProfile->activity_inclination);
                                foreach ($hobbies as $hobbie) {
                                    if ($hobbie == 'Patient Education Material') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?>
                                      />Patient Education</label><br/>
                        <label><input type="checkbox" name="activity_inclination[]" value="Patient Awareness Camps"   <?php
                            if (isset($_POST['activity_inclination'])) {
                                foreach ($_POST['activity_inclination'] as $hobbie) {
                                    if ($hobbie == 'Patient Awareness Camps') {
                                        echo "checked";
                                    }
                                }
                            } else {
                                $hobbies = explode(",", $basicProfile->activity_inclination);
                                foreach ($hobbies as $hobbie) {
                                    if ($hobbie == 'Patient Awareness Camps') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?>
                                      />Patient Awareness Camps</label><br/>

                        <label><input type="checkbox" name="activity_inclination[]" value="Paramedics training sessions" <?php
                            if (isset($_POST['activity_inclination'])) {
                                foreach ($_POST['activity_inclination'] as $hobbie) {
                                    if ($hobbie == 'Paramedics training sessions') {
                                        echo "checked";
                                    }
                                }
                            } else {
                                $hobbies = explode(",", $basicProfile->activity_inclination);
                                foreach ($hobbies as $hobbie) {
                                    if ($hobbie == 'Paramedics training sessions') {
                                        echo "checked";
                                    }
                                }
                            }
                            ?>/>Paramedics training sessions</label><br/>
                        <label><input type="checkbox" name="activity_inclination[]" id="specifyCheck1" value=""  />Workshops if Any ( Pl.  Specify)</label><br/>
                        <label><input type="text" name="activity_inclination[]" maxlength="30" value=""   id="specify2" style="display:none"  class="form-control" style="width:50%" />
                    </div>
                </div>
            </div>
            <div class="row"><div class="col-lg-12"><label>Type Of Practice</label></div></div>
            <div class="row" style="margin-bottom:1em;" id="items">
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Gen Opthal</label>
                    <div class="form-group input-group customWidth">
                        <input type="text" name="gen_ophthal" maxlength="30" value="<?php
                        if (isset($_POST['gen_ophthal'])) {
                            echo $_POST['gen_ophthal'];
                        } else {
                            echo $basicProfile->gen_ophthal;
                        }
                        ?>" class="form-control common"
                               id="id1"/>
                        <span class="input-group-addon">%</span>
                    </div>	
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Retina</label>
                    <div class="form-group input-group customWidth">
                        <input type="text" name="retina" maxlength="30" value="<?php
                        if (isset($_POST['retina'])) {
                            echo $_POST['retina'];
                        } else {
                            echo $basicProfile->retina;
                        }
                        ?>"  class="form-control common"
                               id="id2"/>
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">.
                    <label>Glaucoma</label>
                    <div class="form-group input-group customWidth">
                        <input type="text" name="glaucoma" maxlength="30" value="<?php
                        if (isset($_POST['glaucoma'])) {
                            echo $_POST['glaucoma'];
                        } else {
                            echo $basicProfile->glaucoma;
                        }
                        ?>" class="form-control common"
                               id="id3" /> 
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Cornea</label>
                    <div class="form-group input-group customWidth">
                        <input type="text" name="cornea" maxlength="30" value="<?php
                        if (isset($_POST['cornea'])) {
                            echo $_POST['cornea'];
                        } else {
                            echo $basicProfile->cornea;
                        }
                        ?>"   class="form-control common"
                               id="id4"/>
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Any Other</label>
                    <input class="form-control common" name="any_other" placeholder="Specify Here" style="margin-bottom: 5px" value="<?php
                    if (isset($_POST['any_other'])) {
                        echo $_POST['any_other'];
                    } else {
                        echo $basicProfile->any_other;
                    }
                    ?>">
                    <div class="form-group input-group customWidth">
                        <input type="text" name="other" maxlength="30" value="<?php
                        if (isset($_POST['other'])) {
                            echo $_POST['other'];
                        } else {
                            echo $basicProfile->other;
                        }
                        ?>"   class="form-control common"
                               id="id5"/>
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Total</label>
                    <div class="form-group input-group customWidth">
                        <input type="text" name="total" maxlength="30" value="<?php
                        if (isset($_POST['total'])) {
                            echo $_POST['total'];
                        } else {
                            echo $basicProfile->other + $basicProfile->cornea + $basicProfile->glaucoma + $basicProfile->retina +
                            $basicProfile->gen_ophthal;
                        }
                        ?>" readonly id="id6" class="form-control " />
                        <span class="input-group-addon">%</span>
                    </div>
                    <div class="row">
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
                    } else {
                        echo $basicProfile->daily_opd;
                    }
                    ?>"  class="form-control customWidth" placeholder="No. of Patients"/>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>Average Value Per Rx </label>
                    <input type="text" name="value_per_rx" maxlength="30" value="<?php
                    if (isset($_POST['value_per_rx'])) {
                        echo $_POST['value_per_rx'];
                    } else {
                        echo $basicProfile->value_per_rx;
                    }
                    ?>"  class="form-control customWidth" placeholder="Enter value in Rs." />
                </div>
                <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12 ">
                    <label>Average Surgery Per Month </label>
                    <input type="text" name="value_per_month" maxlength="30" value="<?php
                    if (isset($_POST['value_per_month'])) {
                        echo $_POST['value_per_month'];
                    } else {
                        echo $basicProfile->value_per_month;
                    }
                    ?>"  class="form-control customWidth" placeholder="Enter No. of Surgeries" />
                </div>
                <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12 ">
                    <label>Pharma Potential </label>
                    <input type="text" name="pharma_potential" maxlength="30" value="<?php
                    if (isset($_POST['pharma_potential'])) {
                        echo $_POST['pharma_potential'];
                    } else {
                        echo $basicProfile->pharma_potential;
                    }
                    ?>" class="form-control customWidth"  placeholder="Enter value in Rs."/>
                </div>
                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12 ">
                    <label>MSL Code</label>
                    <input type="text" name="msl_code" maxlength="30" value="<?php
                    if (isset($_POST['msl_code'])) {
                        echo $_POST['msl_code'];
                    } else {
                        echo $basicProfile->msl_code;
                    }
                    ?>" class="form-control customWidth" />
                </div>
            </div>
            <hr/>
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12 ">
                    <input type="submit" name="submit1" value="Save" class="btn btn-primary " onclick="return validate()"/>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<script>
    function validate() {
        var total = $("#id6").val();

        if (total !== '100') {
            alert("Total Has to be hundred");
            return false;
        }
    }
</script>
<?php require_once("layouts/TMfooter.php"); ?>