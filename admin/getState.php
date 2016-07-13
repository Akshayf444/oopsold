<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
if (isset($_POST['search_term'])) {
    $zone = $_POST['search_term'];
    $employees = Employee::find_state($zone);
    if (!empty($employees)) {
        ?>
        <select  onchange="Search1()" class="selectState form-control" >
            <option value="">Select State</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?php echo $employee->state; ?>"><?php echo $employee->state; ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}else {
    ?>

    <select  onchange="Search1()" class="selectState form-control" >
        <option value="">Select State</option>
    </select>
    <?php }
?>