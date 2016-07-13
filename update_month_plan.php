<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}
$empid = $_SESSION['employee'];
require_once(dirname(__FILE__) . "/includes/initialize.php");

$AreaList = Doctor::areaList($_SESSION['employee']);
$Planning = Planning::find_by_id($_GET['id']);
?>
<link href="css/chosen.min.css" rel="stylesheet" type="text/css"/>
<script src="js/chosen.jquery.min.js" type="text/javascript"></script>
<script src="js/chosen.proto.js" type="text/javascript"></script>

<div id="fullCalModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 >Edit Monthly Planning</h4>
            </div>
            <form action="" method="post">
                <div  class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" >
                    <?php
                    $currentMonth = $_SESSION['_CURRENT_MONTH'];
                    $plannningDate = date('m', strtotime($Planning->date));
                    $visit_approved = new VisitApproval();
                    $visitEntryExist = $visit_approved->find_by_date_empid($Planning->date , $empid);
                    if ($plannningDate < $currentMonth || !empty($visitEntryExist)) {
                        ?>
                        <div class="form-group">
                            <label>Area</label>

                            <select data-placeholder="Choose Area" class="chosen-select" multiple style="width:350px;" tabindex="4" name="area[]" disabled="disabled">
                                <option value=""></option>
                                <?php
                                $areaList = explode(",", $_GET['area']);
                                foreach (array_unique(json_decode($AreaList)) as $area) {

                                    if (in_array($area, $areaList)) {
                                        echo '<option value="' . $area . '"  selected >' . $area . '</option>';
                                    } else {
                                        echo '<option value="' . $area . '">' . $area . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        
                    <?php } else { ?>
                        <div class="form-group">
                            <label>Area</label>
                            <select data-placeholder="Choose Area" class="chosen-select" multiple style="width:350px;" tabindex="4" name="area[]" >
                                <option value=""></option>
                                <?php
                                $areaList = explode(",", $_GET['area']);
                                foreach (array_unique(json_decode($AreaList)) as $area) {

                                    if (in_array($area, $areaList)) {
                                        echo '<option value="' . $area . '"  selected >' . $area . '</option>';
                                    } else {
                                        echo '<option value="' . $area . '">' . $area . '</option>';
                                    }
                                }
                                ?>

                            </select>
                            <input type="submit" class="btn btn-danger" value="Update" name="submit">
                        </div>
                    <?php } ?>

                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var isIE = navigator.userAgent.indexOf(' MSIE ') > -1;
        if (isIE) {
            $('#BookAppointment').removeClass('fade');
        }
        $("#fullCalModal").modal();
    });
</script>
<script type="text/javascript">
    var config = {
        '.chosen-select': {},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "95%"}
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }
</script>