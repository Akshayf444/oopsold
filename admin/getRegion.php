<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
if (isset($_POST['search_term'])) {
    $state = $_POST['search_term'];
    $employees = Employee::find_region($state);
    if (!empty($employees)) {
        ?>

        <select  onchange="Search2()" class="selectRegion form-control">
            <option value="">Select Region</option>
        <?php foreach ($employees as $employee): ?>
                <option value="<?php echo $employee->region; ?>"><?php echo $employee->region; ?></option>
        <?php endforeach; ?>
        </select>
    <?php }
}else { ?>

    <select  onchange="Search2()" class="selectRegion form-control">
        <option value="">Select Region</option>
    </select>
<?php
}?>