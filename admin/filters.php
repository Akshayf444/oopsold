<?php
require_once("../includes/initialize.php");
if (isset($_POST['zonewise_doctorlist'])) {
    $filters_array = array('zone', 'state', 'region', 'HQ', 'sm', 'bm', 'tm');
    foreach ($filters_array as $value) {
        if (isset($_POST[$value])) {
            ${$value} = $_POST[$value];
        }
    }

    if (isset($_POST['zone']) && isset($_POST['state']) && isset($_POST['region'])) {
        $sql = "SELECT d.*,db.`msl_code`,db.`class` FROM doctors d 
            INNER JOIN `employees` e ON e.`empid` = d.`empid`
            LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` WHERE e.zone = '$zone' AND e.state = '$state' AND e.region = '$region' AND d.`is_delete` = 0 ";
    } elseif (isset($_POST['zone']) && isset($_POST['state'])) {
        $sql = "SELECT d.*,db.`msl_code`,db.`class` FROM doctors d 
            INNER JOIN `employees` e ON e.`empid` = d.`empid`
            LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` WHERE e.zone = '$zone' AND e.state = '$state' AND d.`is_delete` = 0  ";
    } elseif (isset($_POST['zone'])) {
        $sql = "SELECT d.*,db.`msl_code`,db.`class` FROM doctors d 
            INNER JOIN `employees` e ON e.`empid` = d.`empid`
            LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` WHERE e.zone = '$zone' AND d.`is_delete` = 0 ";
    } elseif (isset($_POST['sm'])) {
        $sql = "SELECT d.*,db.`msl_code`,db.`class` FROM doctors d 
            INNER JOIN `employees` e ON e.`empid` = d.`empid`
            INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid`
            INNER JOIN sm  ON sm.`sm_empid` = bm.`sm_empid`
            LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` WHERE sm.`sm_empid` = '$sm' AND d.`is_delete` = 0 ";
    } elseif (isset($_POST['bm'])) {
        $sql = "SELECT d.*,db.`msl_code`,db.`class` FROM doctors d 
            INNER JOIN `employees` e ON e.`empid` = d.`empid`
            INNER JOIN bm ON bm.`bm_empid` = e.`bm_empid`
            LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` WHERE bm.`bm_empid` = '$bm' AND d.`is_delete` = 0 ";
    } elseif (isset($_POST['tm'])) {
        $sql = "SELECT d.*,db.`msl_code`,db.`class` FROM doctors d 
            INNER JOIN `employees` e ON e.`empid` = d.`empid`
            LEFT JOIN `doc_basic_profile` db ON d.`docid` = db.`docid` WHERE e.`empid` = '$tm' AND d.`is_delete` = 0 ";
    }

    $result = QueryWrapper::executeQuery($sql);
    ?>
    <table class="table table-bordered table-hover row-margin-top">
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
        if (!empty($result)) {
            foreach ($result as $doctor) {
                $total = Doctor:: profile_complete_percentage($doctor->docid);
                ?>
                <tr >
                    <td><a href="viewFilteredProfiles.php?docid=<?php echo $doctor->docid ?>"><?php echo $doctor->name; ?></a></td>
                    <td><?php echo isset($doctor->msl_code) ? $doctor->msl_code : "-" ?></td>
                    <td><?php echo isset($doctor->class) ? $doctor->class : "-" ?></td>
                    <td><?php echo $doctor->speciality; ?></td>
                    <td><?php echo $doctor->emailid; ?></td>
                    <td><?php echo $doctor->mobile; ?></td>
                    <td><?php echo $doctor->area; ?></td>
                    <td><?php
                        echo ($total) . " %";
                        ?>
                    </td>
                </tr>
                <?php
                $page_count++;
            }
        } else {
            echo '<tr><td colspan="8">Details Not Found</td></tr>';
        }
        ?>
    </table>
    <?php
} elseif (isset($_POST['emp_lists'])) {
    if (isset($_POST['sm'])) {
        $sm_empid = $_POST['sm'];
        $bm = BM::find_all($sm_empid);
        if (!empty($bm)) {
            echo '<option>Select BM</option>';
            foreach ($bm as $value) {
                echo '<option value="' . $value->bm_empid . '">' . $value->name . '</option>';
            }
        }
    } elseif (isset($_POST['bm'])) {
        $bm_empid = $_POST['bm'];
        $tm = Employee::find_by_bmid($bm_empid);
        if (!empty($tm)) {
            echo '<option>Select TM</option>';
            foreach ($tm as $value) {
                echo '<option value="' . $value->empid . '">' . $value->name . '</option>';
            }
        }
    }
} elseif (isset($_POST['zonewise_activity'])) {
    $filters_array = array('zone', 'state', 'region', 'HQ', 'sm', 'bm', 'tm');
    foreach ($filters_array as $value) {
        if (isset($_POST[$value])) {
            ${$value} = $_POST[$value];
        }
    }
    $conditions = array();
    $conditions[] = 'INNER JOIN `employees` e ON e.`empid` = act.`empid` ';
    if (isset($_POST['zone'])) {
        $conditions[] = " AND e.zone = '" . $_POST['zone'] . "' ";
    }
    if (isset($_POST['state'])) {
        $conditions[] = " AND e.state = '" . $_POST['state'] . "' ";
    }
    if (isset($_POST['region'])) {
        $conditions[] = " AND e.region = '" . $_POST['region'] . "' ";
    }
    $sql = Activity::buildQuery($conditions);
    //echo $sql;

    $Activitys = QueryWrapper::executeQuery($sql);
    ?>
    <table class="table table-bordered table-hover " id="items" >
        <tr>
            <th>Activity Type</th>
            <th>Activity Date</th>
            <th>Doctor Name</th>
            <th>MSL Code</th>
            <th>Expenses</th>
            <th>Total Business</th>
        </tr>
        <?php
        $page_count = 1;
        if (!empty($Activitys)) {
            foreach ($Activitys as $Activity) {
                ?>
                <tr>
                    <td><?php
                        echo isset($Activity->master_type) ? $Activity->master_type : $Activity->activity_type;
                        ?>
                    </td>
                    <td><?php echo date('d-m-Y', strtotime($Activity->activity_date)); ?></td>
                    <td><?php
                        echo $Activity->name;
                        ?>
                    </td>
                    <td><?php
                        echo (isset($Activity->msl_code)) ? $Activity->msl_code : "-"
                        ?>
                    </td>
                    <td><?php echo $Activity->expances; ?></td>
                    <td>
                        <?php
                        echo $Activity->total;
                        ?>
                    </td>
                    <td><a id="<?php echo $Activity->act_id; ?>" href="#"  class="btn btn-success btn-xs " >View Details</a></td>
                </tr>
                <?php
                $page_count++;
            }
        }
        ?>
    </table>
    <script>
        $('.btn').click(function () {
            var id = $(this).attr('id');
            $.ajax({
                type: 'POST',
                data: {act_id: id},
                url: 'viewActivityDetails.php',
                success: function (data) {
                    $("#modalpopup").html(data);
                }
            });
        });

    </script>
    <?php
}
?>