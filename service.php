<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

require_once(dirname(__FILE__) . "/includes/initialize.php");
$trimmed_array = array();
$errors2 = array();
$error = array();
$doctorName = Doctor::find_by_docid($_GET['docid']);
$service = Services::find_by_docid($_GET['docid']);
$count = Services::count_all($doctorName->empid);
if (!empty($service)) {
    redirect_to("viewServiceDetails.php?docid=$service->docid");
}

if (isset($_POST['submit'])) {

    $newService = new Services();
    $newService->docid = $_GET['docid'];
    $newService->name = $doctorName->name;
    $newService->empid = $doctorName->empid;
    $newService->id = ++$count;
    if (!empty($_POST['services'])) {
        $newService->service = $_POST['services'];
        if ($_POST['services'] != 'no') {
            if (!empty($_POST['aushadh'])) {
                $newService->aushadh = $_POST['aushadh'];
            } else {
                array_push($errors2, "Provide Services Given to Doctor");
            }
        } else {
            $newService->aushadh = 'No';
            $newService->other_services = 'NA';
        }
    } else {
        array_push($errors2, "Select Yes Or No for services given by doctors");
    }


    for ($i = 1; $i < 7; $i++) {
        if (!empty($_POST['Other' . $i])) {
            if ($_POST['Other' . $i] == 'Others') {
                array_push($trimmed_array, $_POST['Other1' . $i]);
            } else {
                array_push($trimmed_array, $_POST['Other' . $i]);
            }
        }
    }
    $newService->special_rate = $_POST['special_rate'];

    $serviceArray = array('AOIC', 'DOC', 'ESCRS', 'WGC', 'WOC');
    foreach ($serviceArray as $service) {
        if (isset($_POST[$service])) {
            $trimmed_service = array_filter(array_map('trim', $_POST[$service]));
            if (!empty($trimmed_service)) {
                $combine_services = implode(',', $trimmed_service);
            } else {
                array_push($error, 1);
            }
        }
    }

    if (count($error) != 5 || !empty($trimmed_array)) {
        $serviceArray = array('AOIC', 'DOC', 'ESCRS', 'WGC', 'WOC');
        foreach ($serviceArray as $service) {
            if (isset($_POST[$service])) {
                $trimmed_service = array_filter(array_map('trim', $_POST[$service]));
                if (!empty($trimmed_service)) {
                    $combine_services = implode(',', $trimmed_service);
                    $newService->{$service} = $combine_services;
                } else {
                    $newService->{$service} = '';
                }
            } else {
                $newService->{$service} = '';
            }
        }
        if (!empty($trimmed_array)) {
            $newService->Other = join(",", $trimmed_array);
        } else {
            $newService->Other = '';
        }
    } else {
        array_push($errors2, "Please select something for Services By Other Competing Companies ");
    }



    if (!empty($_POST['factors'])) {
        $factors = implode(',', $_POST['factors']);
        $newService->factors = $factors;
    } else {
        array_push($errors2, "Please select something For Activities With Doctors");
    }

    if (!empty($_POST['action_plan'])) {
        $newService->action_plan = $_POST['action_plan'];
    } else {
        $newService->action_plan = "NA";
    }
    //else{array_push($errors2, "choose something");}


    if (empty($errors2)) {
        $newService->create();
        if (isset($_GET['action']) && $_GET['action'] == 'redirect') {
            redirect_to("viewAllProfiles.php?docid=$newService->docid");
        } else {
            redirect_to("viewServiceDetails.php?page=$newService->id");
        }
    }
}
$pageTitle = "Add Service Profile";
require_once("layouts/TMheader.php");
?>
<style>
    @media only screen and (max-width: 800px) {
        #no-more-tables td{
            padding-left: 47%;        
        }
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {

        //Hide div w/id extra
        $("#text1").css("display", "none");
        $("#text2").css("display", "none");
        $(".aushadh").css("display", "none");
        $("#special1").css("display", "none");

        // Add onclick handler to checkbox w/id checkme


        // If checked
        $("#service").click(function () {
            $(".aushadh").toggle();
        });


        $("#yes1").click(function () {
            $("#text1").toggle();
        });

        $("#yes2").click(function () {
            $("#text2").toggle();
        });


        $("#service1").click(function () {
            $(".aushadh").hide();
        });


        $("#special").click(function () {
            $("#special1").toggle();
        });



    });
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Service Profile<small><?php echo "  " . $doctorName->name ?></small></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <ul>
        <?php foreach ($errors2 as $val) { ?>
            <li style="color:red;list-style-type:none"><?php echo $val; ?></li>
        <?php } ?>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-sx-12">
        <form action="service.php?docid=<?php echo $doctorName->docid; ?>" method="post">  
            <div class="row" style="margin-bottom:1em;">
                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label>Services provided to Doctor *</label>
                    <div class="checkbox">
                        <label><input type="radio" name="services" id="service" value="yes" <?php
                            if (isset($_POST['services']) && $_POST['services'] == 'yes') {
                                echo "checked";
                            }
                            ?> >Yes</label>
                        <label><input type="radio" name="services"  value="no" id="service1" <?php
                            if (isset($_POST['services']) && $_POST['services'] == 'no') {
                                echo "checked";
                            }
                            ?> >No</label>
                    </div>
                </div>
            </div>
            <div class="row aushadh" style="margin-bottom:1em;">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12 form-group">
                    <label>Pls Specify</label>
                    <input type="text" name="aushadh" class="form-control">
                </div>
            </div>
            <div class="row " style="margin-bottom:1em;">
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12 form-group">
                    <label>Activities With Doctors *</label>
                    <div class="checkbox">
                        <label><input type="checkbox" name="factors[]" value='Regular Visit' <?php
                            if (isset($_POST['factors'])) {
                                foreach ($_POST['factors'] as $value) {
                                    if ($value == 'Regular Visit') {
                                        echo 'checked';
                                    }
                                }
                            }
                            ?> >Regular Visit</label><br/>
                        <label><input type="checkbox" name="factors[]" value='Gift' <?php
                            if (isset($_POST['factors'])) {
                                foreach ($_POST['factors'] as $value) {
                                    if ($value == 'Gift') {
                                        echo 'checked';
                                    }
                                }
                            }
                            ?> >Gift</label><br/>
                        <label><input type="checkbox" name="factors[]" value='Activity' <?php
                            if (isset($_POST['factors'])) {
                                foreach ($_POST['factors'] as $value) {
                                    if ($value == 'Activity') {
                                        echo 'checked';
                                    }
                                }
                            }
                            ?> >Activity</label><br/>
                        <label><input type="checkbox" name="factors[]" value='Sponsorship' <?php
                            if (isset($_POST['factors'])) {
                                foreach ($_POST['factors'] as $value) {
                                    if ($value == 'Sponsorship') {
                                        echo 'checked';
                                    }
                                }
                            }
                            ?> >Sponsorship</label><br/>
                        <label><input type="checkbox" name="factors[]" value='Discounts/Deals on Products' <?php
                            if (isset($_POST['factors'])) {
                                foreach ($_POST['factors'] as $value) {
                                    if ($value == 'Discounts/Deals on Products') {
                                        echo 'checked';
                                    }
                                }
                            }
                            ?>>Discounts/Deals on Products</label><br/>
                        <label><input type="checkbox" name="factors[]" value='Patient Awareness' <?php
                            if (isset($_POST['factors'])) {
                                foreach ($_POST['factors'] as $value) {
                                    if ($value == 'Patient Awareness') {
                                        echo 'checked';
                                    }
                                }
                            }
                            ?> >Patient Awareness</label><br/>
                        <label><input type="checkbox" name="factors[]" value='Aushadh' <?php
                            if (isset($_POST['factors'])) {
                                foreach ($_POST['factors'] as $value) {
                                    if ($value == 'Aushadh') {
                                        echo 'checked';
                                    }
                                }
                            }
                            ?>>Aushadh</label><br/>
                    </div>
                </div>

            </div>	

            <div class="row"><div class="col-lg-12"><label>Services By Other Competing Companies</label></div></div>
            <div class="row  ">
                <div class="col-lg-12 col-xs-12 custom-no-more-table" id="no-more-tables">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Sun </th>
                                <th>Allergan</th>
                                <th>Alcon</th>
                                <th>Micro</th>
                                <th>Ajanta</th>
                                <th>Others (Pls specify)</th>
                            </tr>
                        </thead>
                        <tr>
                            <th>High Value Gifts </th>
                            <td  data-title="Sun" align="center" valign="middle"><input type="checkbox"   value="Sun" name="AOIC[]" <?php
                                if (isset($_POST['AOIC'])) {
                                    foreach ($_POST['AOIC'] as $value) {
                                        if ($value == 'Sun') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>>
                            </td>
                            <td data-title="Allergan" align="center" valign="middle"><input type="checkbox"   value="Allergan" name="AOIC[]" <?php
                                if (isset($_POST['AOIC'])) {
                                    foreach ($_POST['AOIC'] as $value) {
                                        if ($value == 'Allergan') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Alcon" align="center" valign="middle"><input type="checkbox"  value="Alcon" name="AOIC[]" <?php
                                if (isset($_POST['AOIC'])) {
                                    foreach ($_POST['AOIC'] as $value) {
                                        if ($value == 'Alcon') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Micro" align="center" valign="middle"><input type="checkbox"   value="Micro" name="AOIC[]" <?php
                                if (isset($_POST['AOIC'])) {
                                    foreach ($_POST['AOIC'] as $value) {
                                        if ($value == 'Micro') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td  data-title="Ajanta" align="center" valign="middle"><input type="checkbox"   value="Ajanta" name="AOIC[]" <?php
                                if (isset($_POST['AOIC'])) {
                                    foreach ($_POST['AOIC'] as $value) {
                                        if ($value == 'Ajanta') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> >
                            </td>
                            <td data-title="Other" align="center" valign="middle"><input type="checkbox"   value="Other"  class="Other" <?php
                                if (isset($_POST['AOIC'])) {
                                    foreach ($_POST['AOIC'] as $value) {
                                        if ($value == 'Other') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> >
                                <input type="text" class="form-control specify" placeholder="Pls Specify Here" name="AOIC[]"  style="width: 50%">
                            </td>
                        </tr>
                        <tr>
                            <th id="special">Special Rate </th>

                            <td data-title="Sun" align="center" valign="middle"><input type="checkbox"   value="Sun" name="DOC[]" <?php
                                if (isset($_POST['DOC'])) {
                                    foreach ($_POST['DOC'] as $value) {
                                        if ($value == 'Sun') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Allergan" align="center" valign="middle"><input type="checkbox"   value="Allergan" name="DOC[]" <?php
                                if (isset($_POST['DOC'])) {
                                    foreach ($_POST['DOC'] as $value) {
                                        if ($value == 'Allergan') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Alcon" align="center" valign="middle"><input type="checkbox"   value="Alcon" name="DOC[]" <?php
                                if (isset($_POST['DOC'])) {
                                    foreach ($_POST['DOC'] as $value) {
                                        if ($value == 'Alcon') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Micro" align="center" valign="middle"><input type="checkbox"   value="Micro" name="DOC[]" <?php
                                if (isset($_POST['DOC'])) {
                                    foreach ($_POST['DOC'] as $value) {
                                        if ($value == 'Micro') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Ajanta" align="center" valign="middle"><input type="checkbox"   value="Ajanta" name="DOC[]" <?php
                                if (isset($_POST['DOC'])) {
                                    foreach ($_POST['DOC'] as $value) {
                                        if ($value == 'Ajanta') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>></td>
                            <td data-title="Other" align="center" valign="middle"><input type="checkbox"   value="Other"  class="Other" <?php
                                if (isset($_POST['DOC'])) {
                                    foreach ($_POST['DOC'] as $value) {
                                        if ($value == 'Others') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> >
                                <input type="text" class="form-control specify" placeholder="Pls Specify Here" name="DOC[]"  style="width: 50%">
                            </td>
                        </tr>
                        <tr id="special1">
                            <td ><input type="text" class="form-control " placeholder="Pls Specify Here" name="special_rate" placeholder="Pls Specify Details Of Special Rate" ></td>
                        </tr>
                        <tr>
                            <th>Bulk Sampling </th>
                            <td data-title="Sun" align="center" valign="middle"><input type="checkbox"   value="Sun" name="ESCRS[]"  <?php
                                if (isset($_POST['ESCRS'])) {
                                    foreach ($_POST['ESCRS'] as $value) {
                                        if ($value == 'Sun') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>></td>
                            <td data-title="Allergan" align="center" valign="middle"><input type="checkbox"   value="Allergan" name="ESCRS[]" <?php
                                if (isset($_POST['ESCRS'])) {
                                    foreach ($_POST['ESCRS'] as $value) {
                                        if ($value == 'Allergan') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Alcon" align="center" valign="middle"><input type="checkbox"   value="Alcon" name="ESCRS[]" <?php
                                if (isset($_POST['ESCRS'])) {
                                    foreach ($_POST['ESCRS'] as $value) {
                                        if ($value == 'Alcon') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Micro" align="center" valign="middle"><input type="checkbox"   value="Micro" name="ESCRS[]" <?php
                                if (isset($_POST['ESCRS'])) {
                                    foreach ($_POST['ESCRS'] as $value) {
                                        if ($value == 'Micro') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>></td>
                            <td data-title="Ajanta" align="center" valign="middle"><input type="checkbox"   value="Ajanta" name="ESCRS[]" <?php
                                if (isset($_POST['ESCRS'])) {
                                    foreach ($_POST['ESCRS'] as $value) {
                                        if ($value == 'Ajanta') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>></td>
                            <td data-title="Other" align="center" valign="middle"><input type="checkbox"   value="Other"  class="Other" <?php
                                if (isset($_POST['ESCRS'])) {
                                    foreach ($_POST['ESCRS'] as $value) {
                                        if ($value == 'Other') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> >
                                <input type="text" class="form-control specify" name="ESCRS[]" placeholder="Pls Specify Here"  style="width: 50%">
                            </td>
                        </tr>
                        <tr>
                            <th>Post-op pouches / cards </th>
                            <td data-title="Sun" align="center" valign="middle"><input type="checkbox"   value="Sun" name="WGC[]" <?php
                                if (isset($_POST['WGC'])) {
                                    foreach ($_POST['WGC'] as $value) {
                                        if ($value == 'Sun') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>></td>
                            <td data-title="Allergan" align="center" valign="middle"><input type="checkbox"   value="Allergan" name="WGC[]" <?php
                                if (isset($_POST['WGC'])) {
                                    foreach ($_POST['WGC'] as $value) {
                                        if ($value == 'Allergan') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>></td>
                            <td data-title="Alcon" align="center" valign="middle"><input type="checkbox"   value="Alcon" name="WGC[]" <?php
                                if (isset($_POST['WGC'])) {
                                    foreach ($_POST['WGC'] as $value) {
                                        if ($value == 'Alcon') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>></td>
                            <td data-title="Micro" align="center" valign="middle"><input type="checkbox"   value="Micro" name="WGC[]" <?php
                                if (isset($_POST['WGC'])) {
                                    foreach ($_POST['WGC'] as $value) {
                                        if ($value == 'Micro') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Ajanta" align="center" valign="middle"><input type="checkbox"   value="Ajanta" name="WGC[]" <?php
                                if (isset($_POST['WGC'])) {
                                    foreach ($_POST['WGC'] as $value) {
                                        if ($value == 'Ajanta') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Other" align="center" valign="middle"><input type="checkbox"   value="Other"  class="Other" <?php
                                if (isset($_POST['WGC'])) {
                                    foreach ($_POST['WGC'] as $value) {
                                        if ($value == 'Other') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> >
                                <input type="text" class="form-control specify" placeholder="Pls Specify Here" name="WGC[]"  style="width: 50%">
                            </td>
                        </tr>
                        <tr>
                            <th>Journals/Books/Online Subscription </th>
                            <td data-title="Sun" align="center" valign="middle"><input type="checkbox"   value="Sun" name="WOC[]" <?php
                                if (isset($_POST['WOC'])) {
                                    foreach ($_POST['WOC'] as $value) {
                                        if ($value == 'Sun') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Allergan" align="center" valign="middle"><input type="checkbox"   value="Allergan" name="WOC[]"  <?php
                                if (isset($_POST['WOC'])) {
                                    foreach ($_POST['WOC'] as $value) {
                                        if ($value == 'Allergan') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?>> </td>
                            <td data-title="Alcon" align="center" valign="middle"><input type="checkbox"   value="Alcon" name="WOC[]" <?php
                                if (isset($_POST['WOC'])) {
                                    foreach ($_POST['WOC'] as $value) {
                                        if ($value == 'Alcon') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Micro" align="center" valign="middle"><input type="checkbox"   value="Micro" name="WOC[]" <?php
                                if (isset($_POST['WOC'])) {
                                    foreach ($_POST['WOC'] as $value) {
                                        if ($value == 'Micro') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> ></td>
                            <td data-title="Ajanta" align="center" valign="middle"><input type="checkbox"   value="Ajanta" name="WOC[]" <?php
                                if (isset($_POST['WOC'])) {
                                    foreach ($_POST['WOC'] as $value) {
                                        if ($value == 'Ajanta') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> >
                            </td>
                            <td data-title="Other" align="center" valign="middle"><input type="checkbox"   value="Other"  class="Other" <?php
                                if (isset($_POST['WOC'])) {
                                    foreach ($_POST['WOC'] as $value) {
                                        if ($value == 'Other') {
                                            echo 'checked';
                                        }
                                    }
                                }
                                ?> >
                                <input type="text" class="form-control specify" placeholder="Pls Specify Here" name="WOC[]" style="width: 50%">
                            </td>
                        </tr>
                        <tr>
                            <th id='conferences'>Conferences(Pls specify) </th>
                            <td data-title="Sun" align="center" valign="middle" class="conferences2">

                                <select name="Other1" class="selection">
                                    <option></option>
                                    <option value="Sun-AIOC">AIOC</option>
                                    <option value="Sun-DOC">DOC</option>
                                    <option value="Sun-ESCRS">ESCRS</option>
                                    <option value="Sun-WGC">WGC</option>
                                    <option value="Sun-WOC">WOC</option>
                                    <option value="Others">Others</option>                                  
                                </select>
                                <input type="text" class="form-control specify2" placeholder="Pls Specify Here" name="Other11">
                            </td>
                            <td data-title="Allergan" align="center" valign="middle" class="conferences2">
                                <select name="Other2" class="selection">
                                    <option></option>
                                    <option value="Allergan-AIOC">AIOC</option>
                                    <option value="Allergan-DOC">DOC</option>
                                    <option value="Allergan-ESCRS">ESCRS</option>
                                    <option value="Allergan-WGC">WGC</option>
                                    <option value="Allergan-WOC">WOC</option>
                                    <option value="Others">Others</option>                               
                                </select>
                                <input type="text" class="form-control specify2" placeholder="Pls Specify Here" name="Other12">
                            </td>
                            <td data-title="Alcon" align="center" valign="middle" class="conferences2">

                                <select name="Other3" class="selection">
                                    <option></option>
                                    <option value="Alcon-AIOC">AIOC</option>
                                    <option value="Alcon-DOC">DOC</option>
                                    <option value="Alcon-ESCRS">ESCRS</option>
                                    <option value="Alcon-WGC">WGC</option>
                                    <option value="Alcon-WOC">WOC</option>
                                    <option value="Others">Others</option>                                  
                                </select>
                                <input type="text" class="form-control specify2" placeholder="Pls Specify Here" name="Other13">

                            </td>
                            <td data-title="Micro" align="center" valign="middle" class="conferences2">
                                <select name="Other4" class="selection">
                                    <option></option>
                                    <option value="Micro-AIOC">AIOC</option>
                                    <option value="Micro-DOC">DOC</option>
                                    <option value="Micro-ESCRS">ESCRS</option>
                                    <option value="Micro-WGC">WGC</option>
                                    <option value="Micro-WOC">WOC</option>
                                    <option value="Others">Others</option>                                  
                                </select>
                                <input type="text" class="form-control specify2" placeholder="Pls Specify Here" name="Other14">
                            </td>
                            <td data-title="Ajanta" align="center" valign="middle" class="conferences2">

                                <select name="Other5" class="selection">
                                    <option></option>
                                    <option value="Ajanta-AIOC">AIOC</option>
                                    <option value="Ajanta-DOC">DOC</option>
                                    <option value="Ajanta-ESCRS">ESCRS</option>
                                    <option value="Ajanta-WGC">WGC</option>
                                    <option value="Ajanta-WOC">WOC</option>
                                    <option value="Others">Others</option>                                  
                                </select>
                                <input type="text" class="form-control specify2" placeholder="Pls Specify Here" name="Other15">
                            </td>
                            <td data-title="Other" align="center" valign="middle" class="conferences2">
                                <select name="Other[]" class="selection">
                                    <option></option>
                                    <option value="AIOC">AIOC</option>
                                    <option value="DOC">DOC</option>
                                    <option value="ESCRS">ESCRS</option>
                                    <option value="WGC">WGC</option>
                                    <option value="WOC">WOC</option>
                                    <option value="Others">Others</option>                                
                                </select>
                                <input type="text" class="form-control specify2" placeholder="Pls Specify Here" name="Other16">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="7">
                                <input type="submit" name="submit" value="Save" class="btn btn-primary" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        $(".specify").css("display", "none");
        $(".Other").click(function () {
            $(this).next('.specify').css({'display': 'block'});
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {

        $(".specify2").css("display", "none");

        $('.selection').change(function () {
            if ($(this).val() == 'Others') {
                $(this).next('.specify2').css({'display': 'block'});
            } else {
                $(this).next('.specify2').css({'display': 'none'});
            }
        });
    });
</script>

<?php require_once("layouts/TMfooter.php"); ?>