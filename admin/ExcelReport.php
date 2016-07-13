<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");
$employees = Employee::find_all();
$pageTitle = "Excel Report";

if (isset($_POST['excel'])) {
    $sql = "Select * FROM employees" ;
    $excelReport = new photograph();
    $excelReport->generateExcel($sql);    
}
require_once("adminheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Excel Report</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <form action="" method="post">
        <input type="submit" name="excel" value ="Report">
    </form>
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 table-responsive">
        <table class="table table-bordered table-hover">
            <tr>
                <th>SM-Id</th>
                <th>SM-Name</th>
                <th>BM-Id</th>
                <th>BM-Name</th>
                <th>Emp-Id</th>
                <th>Emp Name</th>

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
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php
require_once("adminfooter.php");
