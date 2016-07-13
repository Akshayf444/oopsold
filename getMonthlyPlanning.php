<?php
if (isset($_POST['month']) && isset($_POST['year'])) {
    $year = $_POST['year'];
    $month = $_POST['month'];
    $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for ($i = 1; $i <= $number_of_days; $i++) {
        $date = $year . '-' . $month . '-' . $i;
        ?>
        <tr>
            <td class="calendar">
                <input type="hidden" class="calendar" value="<?php echo date('Y-m-d', strtotime($date)); ?>" name="date[]">
                <div class="date">
                    <span class="binds"></span>
                    <span class="month"><?php echo date("M ", strtotime($date)); ?></span>
                    <span class="day"><b><?php echo $i; ?></b></span>
                </div>
            </td>
            <td>
                <input type="text" value="" data-role="tagsinput" name="area[]" class="areaList form-control" />
            </td>
        </tr>
        <?php
    }

    echo '<tr> <td colspan="2"> <input type="submit" class="btn btn-primary" value="Save" name="submit"> </td></tr>';
}
?>

