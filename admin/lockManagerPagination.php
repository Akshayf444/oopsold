<?php
require_once("../includes/initialize.php");
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 50;
$total_count = Employee::count();
$pagination = new Pagination($page, $per_page, $total_count);

$sql = "SELECT * FROM employees ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";
$employees = Employee::find_by_sql($sql);
?>
<div  class="col-lg-12 col-md-12 col-sm-12  col-xs-12 table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>SM-Id</th>
            <th>SM-Name</th>
            <th>BM-Id</th>
            <th>BM-Name</th>
            <th>Emp-Id</th>
            <th>Emp Name</th>
            <th>Lock Basic Profile</th>
            <th>Lock Service Profile</th>
            <th>Lock Business Profile</th>
            <th>Lock academic Profile</th>
        </tr>
        <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?php
                    $bmName = BM::find_by_bmid($employee->bm_empid);
                    $smName = isset($bmName->sm_empid) ? SM::find_by_smid($bmName->sm_empid) : "";
                    echo isset($bmName->sm_empid) ? $bmName->sm_empid : "-";
                    ?>
                </td>
                <td><?php echo isset($smName->name) ? $smName->name : "-"; ?></td>
                <td><?php echo isset($employee->bm_empid) ? $employee->bm_empid : "-"; ?></td>
                <td><?php echo isset($bmName->name) ? $bmName->name : "-"; ?></td>
                <td id="<?php echo $employee->empid; ?>"><?php echo $employee->empid; ?></td>
                <td><?php echo $employee->name; ?></td>
                <td><input type="checkbox" name="lockbasic" 
                    <?php
                    if ($employee->lock_basic == 1) {
                        echo "checked";
                    }
                    ?> id="<?php echo $employee->empid . '_basic'; ?>" >
                </td>
                <td><input type="checkbox" name="lockservice" 
                    <?php
                    if ($employee->lock_service == 1) {
                        echo "checked";
                    }
                    ?> id="<?php echo $employee->empid . '_service'; ?>">
                </td>
                <td><input type="checkbox" name="lockbuisness"
                    <?php
                    if ($employee->lock_buisness == 1) {
                        echo "checked";
                    }
                    ?> id="<?php echo $employee->empid . '_buisness'; ?>">
                </td>
                <td><input type="checkbox" name="lockacademic" 
                    <?php
                    if ($employee->lock_academic == 1) {
                        echo "checked";
                    }
                    ?> id="<?php echo $employee->empid . '_academic'; ?>">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<div class ="row">
    <div class="col-lg-12" style="clear: both;">
        <?php
        if ($pagination->total_pages() > 1) {

            echo '<div class="col-lg-10" style = "text-align:center">';
            for ($i = 1; $i <= $pagination->total_pages(); $i++) {
                if ($i == $page) {
                    ?>
                    <input type="button" class="btn btn-warning btn-xs"  value="<?php echo $i; ?>" onclick="sendPaginationRequest(this.value)">
                <?php } else {
                    ?>
                    <input type="button" class="btn btn-xs btn-red"  value="<?php echo $i; ?>" onclick="sendPaginationRequest(this.value)">

                <?php
                }
            }
            echo '</div>';
        }
        ?>
    </div>
</div>
<script>
//For locking and Unlocking of profiles
    $(document).ready(function () {
        $(":checkbox").click(function () {
            var search_term = this.id;
            if ($(this).prop("checked")) {

                //Send request to lock page 
                $.post('lockProfiles.php', {search_term: search_term}, function (data) {
                    $("#result").css("background", "#fff");
                });

            } else {
                //Send request to unlock page

                $.post('unlockProfiles.php', {search_term: search_term}, function (data) {
                    $("#result").css("background", "#fff");
                });

            }
        });
    });
</script>