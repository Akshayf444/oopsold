<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:logout.php");
}
require_once("../includes/initialize.php");
$pageTitle = "List All Doctors";

function ZoneList($id = "") {
    $Zones = Employee::find_zone();
    if (!empty($Zones)) {
        foreach ($Zones as $Zone) {
            if ($Zone->Zone == $id) {
                echo "<option value = '" . $Zone->Zone . "' selected >" . $Zone->Zone . "</option>";
            } else {
                echo "<option value = '" . $Zone->Zone . "' >" . $Zone->Zone . "</option>";
            }
        }
    }
}

if (isset($_GET['zonewise_filter'])) {
    $filters_array = array('zone', 'state', 'region', 'HQ', 'sm', 'bm', 'tm');
    foreach ($filters_array as $value) {
        if (isset($_GET[$value])) {
            ${$value} = $_GET[$value];
        }
    }

    if (isset($_GET['zone']) && $_GET['zone'] != '' && isset($_GET['state']) && $_GET['state'] != '' && isset($_GET['region']) && $_GET['region'] != '') {
        $conditions = array(" WHERE e.zone = '" . $zone . "' AND e.state = '" . $state . "' AND e.region = '" . $region . " ' ");
        $doctors = Doctor::buildQuery($conditions);
    } elseif (isset($_GET['zone']) && $_GET['zone'] != '' && isset($_GET['state']) && $_GET['state'] != '') {
        $conditions = array(" WHERE e.zone = '" . $zone . "' AND e.state = '" . $state . "' ");
        $doctors = Doctor::buildQuery($conditions);
    } elseif (isset($_GET['zone']) && $_GET['zone'] != '') {
        $conditions = array("WHERE e.zone = '" . $zone . "' ");
        $doctors = Doctor::buildQuery($conditions);
    } elseif (isset($_GET['sm']) && $_GET['sm'] != '') {
        $conditions = array('INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` ', 'INNER JOIN sm  ON sm.`sm_empid` = bm.`sm_empid`', 'AND sm.`sm_empid` = ' . $sm);
        $doctors = Doctor::buildQuery($conditions);
    } elseif (isset($_GET['bm']) && $_GET['bm'] != '') {
        $conditions = array('INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid` ', 'AND e.`bm_empid` = ' . $bm);
        $doctors = Doctor::buildQuery($conditions);
    } elseif (isset($_GET['tm']) && $_GET['tm'] != '') {
        $conditions = array('AND e.`empid` = ' . $tm);
        $doctors = Doctor::buildQuery($conditions);
    }

    // $doctors = QueryWrapper::executeQuery($sql);
} else {
    if (isset($_GET['complete'])) {
        $sql = "SELECT d.* FROM doctors d
        INNER JOIN `doc_basic_profile` db ON d.`docid` = db.`docid`
        INNER JOIN services ser ON d.`docid` = ser.`docid`
        INNER JOIN `doc_academic_profile` da ON d.`docid` = da.`docid`
        INNER JOIN competitors cmpt ON d.`docid` = cmpt.`docid`
        WHERE d.is_delete = 0 AND db.`activity_inclination` != '' AND
        db.`area1`!= '' AND db.`area2`!= '' AND db.`behaviour`!= '' AND db.`class`!= '' AND
        db.`clinic_name`!= '' AND db.`cornea`!= '' AND db.`daily_opd`!= '' AND db.`DOA`!= '' AND
        db.`DOB`!= '' AND db.`gen_ophthal`!= '' AND db.`glaucoma`!= '' AND
        db.`hobbies`!= '' AND db.`inclination_to_speaker`!= '' AND
        db.`msl_code`!= '' AND db.`name`!= '' AND db.`pharma_potential`!= '' AND
        db.`pincode1`!= '' AND db.`pincode2`!= '' AND db.`plot1`!= '' AND db.`plot2`!= '' AND
        db.`potential_to_speaker`!= '' AND db.`receive_mailers`!= '' AND
        db.`receive_sms`!= '' AND db.`retina`!= '' AND db.`state1`!= '' AND db.`state2`!= '' AND
        db.`street1`!= '' AND db.`street2`!= '' AND db.`total`!= '' AND db.`type`!= '' AND
        db.`value_per_month`!= '' AND db.`value_per_rx`!= '' AND db.`yrs_of_practice` != '' AND db.`city1` != '' AND db.`city2` !='' ";
        $doctors = QueryWrapper::executeQuery($sql);
    } else {
        //$doctors = Doctor::find();
        $conditions = array();
        $page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
        $per_page = 500;
        $total_count = Doctor::all();
        $pagination = new Pagination($page, $per_page, $total_count);

        $paging = ' LIMIT ' . $per_page . ' OFFSET ' . $pagination->offset();
        $doctors = Doctor::buildQuery($conditions, $paging);
    }
}

$stateList = isset($_GET['zone']) && $_GET['zone'] != '' ? states($_GET['zone'], $_GET['state']) : "";
$regionList = isset($_GET['state']) && $_GET['state'] != '' ? regionList($_GET['state'], $_GET['region']) : "";
$bmlist = isset($_GET['sm']) && $_GET['sm'] != '' ? bmList($_GET['sm'], $_GET['bm']) : "";
$tmlist = isset($_GET['bm']) && $_GET['bm'] != '' ? tmList($_GET['bm'], $_GET['tm']) : "";
$smList = isset($_GET['sm']) ? smList($_GET['sm']) : smList();

function states($zone, $state) {
    $output = '';
    $employees = Employee::find_state($zone);
    if (!empty($employees)) {
        foreach ($employees as $employee) {
            if ($state == $employee->state) {
                $output .='<option value="' . $employee->state . '" selected>' . $employee->state . '</option>';
            } else {
                $output .='<option value="' . $employee->state . '">' . $employee->state . '</option>';
            }
        }
    }
    return $output;
}

function smList($sm = null) {
    $output = '';
    $SMs = SM::find();
    foreach ($SMs as $SM) {
        if ($sm == $SM->sm_empid) {
            $output .='<option value = "' . $SM->sm_empid . '" selected> ' . $SM->name . '</option>';
        } else {
            $output .='<option value = "' . $SM->sm_empid . '"> ' . $SM->name . '</option>';
        }
    }
    return $output;
}

function regionList($state, $region) {
    $output = '';
    $employees = Employee::find_region($state);
    //var_dump($employees);
    if (!empty($employees)) {
        foreach ($employees as $employee) {
            if ($region == $employee->region) {
                $output .='<option value="' . $employee->region . '" selected>' . $employee->region . '</option>';
            } else {
                $output .='<option value="' . $employee->region . '">' . $employee->region . '</option>';
            }
        }
    }
    return $output;
}

function tmList($bm, $tmid) {
    $output = '';
    $tm = Employee::find_by_bmid($tm);
    if (!empty($tm)) {
        foreach ($tm as $value) {
            if ($tmid == $value->empid) {
                $output .= '<option value="' . $value->empid . '" selected>' . $value->name . '</option>';
            } else {
                $output .= '<option value="' . $value->empid . '">' . $value->name . '</option>';
            }
        }
    }
    return $output;
}

function bmList($sm, $bmid) {
    $output = '';
    $bm = BM::find_all($sm);
    if (!empty($bm)) {
        foreach ($bm as $value) {
            if ($bmid == $value->bm_empid) {
                $output .= '<option value="' . $value->bm_empid . '" selected>' . $value->name . '</option>';
            } else {
                $output .= '<option value="' . $value->bm_empid . '">' . $value->name . '</option>';
            }
        }
    }

    return $output;
}

require_once("adminheader.php");
echo pageHeading('List Of Doctors');

if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
if (!isset($_GET['complete'])) {
    ?>
    <form action="" method="GET" id="form1">
        <input type="hidden" name="zonewise_filter">
        <div class="row" >
            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
                <select   name="zone" class="zone form-control">
                    <option value="">Select Zone</option>
                    <?php echo isset($_GET['zone']) ? ZoneList($_GET['zone']) : ZoneList(); ?>
                </select>
            </div>

            <div id="state" class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
                <select   name="state" class="selectState form-control" >
                    <option value="">Select State</option>
                    <?php echo $stateList; ?>
                </select>
            </div>

            <div id="region" class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
                <select   name="region" class="selectRegion form-control" >
                    <option value="">Select Region</option>
                    <?php echo $regionList; ?>
                </select>
            </div>
            <div  class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
                <input type="button" name="Fetch" class="btn btn-success" value="Fetch" onclick="this.form.submit()">
            </div>
        </div>

        <div class="row row-margin-top" >
            <div id="sm" class="col-lg-3 col-sm-3 col-md-3 col-xs-3" >
                <select  class="sm form-control" name="sm">
                    <option value="">Select SM</option>
                    <?php echo $smList; ?>
                </select>
            </div>

            <div  class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
                <select  name="bm" class=" form-control"  >
                    <option value="">Select BM</option>
                    <?php echo $bmlist; ?>
                </select>
            </div>

            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-3">
                <select  name="tm" class=" form-control" >
                    <option value="">Select TM</option>
                    <?php echo $tmlist; ?>
                </select>
            </div>
        </div>
    </form>
<?php } ?>
<div id="result">
    <div class="row row-margin-top">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <table class="table table-bordered table-hover ">
                <tr>
                    <th>Name</th>
                    <th>MSL Code</th>
                    <th>Class</th>
                    <th>Speciality</th>
                    <th>Email-Id</th>
                    <th>Mobile No</th>
                    <th>Area</th>
                    <th>Profile Completed</th>

                </tr>
                <?php
                $page_count = 1;
                if (!empty($doctors)) {
                    foreach ($doctors as $doctor) {
                        //$BasicProfile = BasicProfile::find_by_docid($doctor->docid);
                        //$total = Doctor:: profile_complete_percentage($doctor->docid);
                        ?>
                        <tr >
                            <td><a href="viewFilteredProfiles.php?docid=<?php echo $doctor->docid?>"><?php echo $doctor->name; ?></a></td>
                            <td><?php echo $doctor->msl_code ?></td>
                            <td><?php echo $doctor->class ?></td>
                            <td><?php echo $doctor->speciality; ?></td>
                            <td><?php echo $doctor->emailid; ?></td>
                            <td><?php echo $doctor->mobile; ?></td>
                            <td><?php echo $doctor->area; ?></td>
                            <td><?php echo $doctor->percent; ?></td>
                        </tr>
                        <?php
                        $page_count++;
                    }
                } else {
                    echo '<tr><td colspan="8">Details Not Found</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>

    <div class ="row">
        <div class="col-lg-12" style="clear: both;margin-bottom: 10px">
            <?php
            if (!isset($_GET['complete']) && !isset($_GET['zonewise_filter'])) {
                if ($pagination->total_pages() > 1) {

                    echo '<div class="col-lg-10" style = "text-align:center">';
                    for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                        ?>
                        <a class="btn btn-xs btn-primary" href="ListDoctors.php?page=<?php echo $i; ?>" <?php
                           if ($page == $i) {
                               echo 'style="border:1px solid red"';
                           }
                           ?> ><?php echo $i; ?></a>
                           <?php
                       }
                       echo '</div>';
                   }
               }
               ?>
        </div>
    </div>
</div>
<?php require_once("adminfooter.php"); ?>