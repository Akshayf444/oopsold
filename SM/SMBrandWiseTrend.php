<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$empid = $_SESSION['SM'];
$BMs = BM::find_all($empid);
$Brand_unit = 'Value';

function productList($id = "") {
    $Products = Product::find_all();
    $ProductList = '<option value = "" >Select Brand</option>';
    foreach ($Products as $Product) {
        if ($Product->id == $id) {
            $ProductList .='<option value ="' . $Product->id . '" selected >' . $Product->name . '</option>';
        } else {
            $ProductList .='<option value ="' . $Product->id . '" >' . $Product->name . '</option>';
        }
    }
    return $ProductList;
}

function doctor_list($id = "", $empid) {
    $doctors = Doctor::find_all($empid);
    $doctor_list = '';
    foreach ($doctors as $doctor) {
        if ($doctor->docid == $id) {
            $doctor_list .= '<option value="' . $doctor->docid . '" selected>' . $doctor->name . '</option>';
        } else {
            $doctor_list .= '<option value="' . $doctor->docid . '">' . $doctor->name . '</option>';
        }
    }

    return $doctor_list;
}

if (isset($_POST['search_term']) && !isset($_POST['doctor'])) {
    $brandSeriesData = array();
    $brandName = $_POST['search_term'];
    $xAxisData = array();

    $m = 1;
    for ($i = 1; $i <= 12; $i++) {
        $m++;
        $monthData = array();

        $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
        $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

        if ($brandName == "total_business") {
            $brand_busi_list = BrandBusiness::find_by_month_sm($month, $empid);
            foreach ($brand_busi_list as $brand) {
                if (isset($brand->total)) {
                    array_push($monthData, $brand->total);
                } else {
                    array_push($monthData, 0);
                }
            }
            $Brand_unit = 'Value';
        } else {
            $brand_busi_list = BrandBusiness::find_by_month_bm($month, $empid);
            foreach ($brand_busi_list as $brand) {
                for ($i = 1; $i < 9; $i++) {
                    $data = explode(",", $brand->{'brand' . $i});
                    if ($data[0] == $brandName) {
                        array_push($monthData, $data[1]);
                    }
                }
            }
            $Brand_unit = 'No. of Unit';
        }
        array_push($xAxisData, $monthname);
        array_push($brandSeriesData, array_sum($monthData));
    }
}

if (isset($_POST['search_term']) && $_POST['search_term'] != 0 && $_POST['search_term'] != "total_business" && $_POST['doctor_name'] != 0) {
    $brandName = $_POST['search_term'];
    $doctorName = $_POST['doctor_name'];
    $xAxisData = array();
    $finalDoctorList = doctor_list($doctorName,$_POST['employee']);
    $brandSeriesData2 = array();
    $m = 1;
    for ($i = 1; $i <= 12; $i++) {
        $m++;
        $monthData = array();
        $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y')));
        array_push($xAxisData, $monthname);
        $month = date('m', mktime(0, 0, 0, $m, 1, date('Y')));
        $brand_busi_list = BrandBusiness::find_by_docid($month, $doctorName);

        if (!empty($brand_busi_list)) {
            for ($i = 1; $i < 9; $i++) {
                $data = explode(",", $brand_busi_list->{'brand' . $i});
                if ($data[0] == $brandName) {
                    array_push($monthData, $data[1]);
                }
            }

            array_push($brandSeriesData2, array_sum($monthData));
        } else {
            array_push($brandSeriesData2, 0);
        }
    }

    $Brand_unit = 'No. Of Unit';
}

if (isset($_POST['doctor_name']) && $_POST['search_term'] == 0 && $_POST['search_term'] != "total_business") {
    $brandSeriesData = array();
    $doctorName = $_POST['doctor_name'];
    $xAxisData = array();
    $finalDoctorList = doctor_list($doctorName,$_POST['employee']);
    $m = 1;
    for ($i = 1; $i <= 12; $i++) {
        $m++;
        $monthData = array();
        $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
        $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

        $brandList = BrandBusiness::find_by_docid($m, $doctorName);
        if (isset($brandList->total)) {
            array_push($monthData, $brandList->total);
        } else {
            array_push($monthData, 0);
        }
        array_push($xAxisData, $monthname);
        array_push($brandSeriesData, array_sum($monthData));
    }
}


$pageTitle = "Brandwise Trend";
require_once("SMheader.php");
?>
<!--
<script type="text/javascript" src="js/jsapi.js"></script>-->
<script>
    function Search2() {
        $("#Linechart").css("background", " url('images/loader.gif') no-repeat scroll center center ");
        var search_term = $(".employee1").val();
        $.post('SMgetDoctorsList.php', {search_term: search_term}, function (data) {

            //$('#Linechart').css("background","#fff");
            $('#employees1').html(data);
        });
    }

    function Search1() {
        $("#Linechart").css("background", " url('images/loader.gif') no-repeat scroll center center ");
        var search_term2 = $(".employee").val();
        $.post('getData.php', {search_term2: search_term2}, function (data) {

            //$('#employees').css("background","#fff");
            $('#employees').html(data);
        });
    }
</script>
<script>

    $(function () {
        $('#Linechart').highcharts({
            chart: {
                renderTo: 'Linechart',
                defaultSeriesType: 'spline',
                marginRight: 20,
                marginBottom: 20
            },
            title: {
                text: 'Brandwise Business',
                x: -20 //center
            },
            xAxis: {
                categories: <?php echo json_encode($xAxisData); ?>
            },
            yAxis: {
                    <?php
                        if ($Brand_unit == 'Value') {
                            echo 'allowDecimals : true, ';
                        } elseif ($Brand_unit == 'No. Of Unit') {
                            echo 'allowDecimals : false ,';
                        }
                    ?>
                title: {
                    text: 'Buisness'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                    name: 'Value',
                    data: <?php
                            if (isset($brandSeriesData)) {
                                echo json_encode($brandSeriesData, JSON_NUMERIC_CHECK);
                            } elseif (isset($brandSeriesData2)) {
                                echo json_encode($brandSeriesData2, JSON_NUMERIC_CHECK);
                            } else {
                                $brandSeriesData2 = null;
                                $brandSeriesData = null;
                            }
                        ?>
                }]
        });
    });
</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Brandwise Trend</h1>
    </div>      <!-- /.col-lg-12 -->
</div>
<div class="row">
    <form action="SMBrandWiseTrend.php" method="post">
        <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">

            <select name="search_term" onchange="this.form.submit()" class="form-control" id="search_term">
                <?php
                if (isset($_POST['search_term'])) {
                    echo productList($_POST['search_term']);
                } else {
                    echo productList();
                }
                ?>
                <option value="total_business">Total Business</option>
            </select>

        </div>


        <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8" id="employees"> 

            <select  onchange="Search2()" class="employee1 form-control" name="employee">
                <option value="select">Select Employee</option>
                <?php
                foreach ($BMs as $BM) :
                    $employees = Employee::find_by_bmid($BM->bm_empid);
                    foreach ($employees as $employee):
                        ?>
                        <option value="<?php echo $employee->empid; ?>"><?php echo $employee->name; ?></option>
                        <?php
                    endforeach;
                endforeach;
                ?>
            </select>
        </div>
        <div  class="col-lg-3 col-sm-4 col-md-4 col-xs-8">

            <select name="doctor_name" onchange="this.form.submit()" class="form-control" id="employees1">
                <option value="0">Select Doctor</option>
                <?php if (isset($finalDoctorList)) echo $finalDoctorList; ?>
            </select>

        </div>
    </form>
    <div>
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 panel-body"> 
                <div id="Linechart" style="width: 100%; height: 100%;"></div>
            </div>
        </div>
    </div>
</div>
<?php require_once("SMfooter.php"); ?> 