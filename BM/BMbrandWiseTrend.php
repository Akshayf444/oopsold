<?php
session_start();
if (!isset($_SESSION['BM'])) {
    header("Location:login.php");
}
require_once("../includes/initialize.php");
$empid = $_SESSION['BM'];
$employees = Employee::find_by_bmid($empid);

$pageTitle = "Brandwise Trend";
$Brand_unit = 'Value';

function productList($id = "") {
    $Products = Product::find_all();
    $ProductList = '<option value ="0">Select Brand</option>';
    foreach ($Products as $Product) {
        if ($Product->id == $id) {
            $ProductList .='<option value ="' . $Product->id . '" selected >' . $Product->name . '</option>';
        } else {
            $ProductList .='<option value ="' . $Product->id . '" >' . $Product->name . '</option>';
        }
    }
    if ($id == 'total_business') {
        $ProductList .= '<option value="total_business" selected>Total Business</option>';
    } else {
        $ProductList .= '<option value="total_business">Total Business</option>';
    }
    return $ProductList;
}

if (isset($_POST['search_term']) && $_POST['doctor_name'] == 0) {

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
            $brand_busi_list = 0;
            foreach ($employees as $employee) {
                $brand_busi_list += BusiProfile::Month_wise_Buisness($month, $employee->empid);
            }
            if (isset($brand_busi_list)) {
                array_push($monthData, (float) $brand_busi_list);
            } else {
                array_push($monthData, 0);
            }

            $Brand_unit = 'Value';
            $title = 'Total Business';
        } else {
            $brand_busi_list = 0;
            foreach ($employees as $employee) {
                $brand_busi_list = BusiProfile::brandwise_business($brandName, $month, $empid);
            }
            if (isset($brand_busi_list)) {
                array_push($monthData, (int) $brand_busi_list);
            } else {
                array_push($monthData, 0);
            }
            $Brand_unit = 'No. of Unit';
            $title = 'Brandwise Business';
        }
        array_push($xAxisData, $monthname);
        array_push($brandSeriesData, array_sum($monthData));
    }
}
if (isset($_POST['search_term']) && $_POST['search_term'] != 0 && $_POST['search_term'] != "total_business" && $_POST['doctor_name'] != 0) {
    $brandName = $_POST['search_term'];
    $doctorName = $_POST['doctor_name'];
    $xAxisData = array();

    $brandSeriesData2 = array();
    $m = 1;
    for ($i = 1; $i <= 12; $i++) {
        $m++;
        $monthData = array();
        $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y')));
        array_push($xAxisData, $monthname);
        $month = date('m', mktime(0, 0, 0, $m, 1, date('Y')));
        $brand_busi_list = BusiProfile::doc_brand_wise_business($brandName, $doctorName, $month);
        if (!empty($brand_busi_list)) {
            array_push($brandSeriesData2, (int) $brand_busi_list);
        } else {
            array_push($brandSeriesData2, 0);
        }
    }

    $title = 'Doctor Wise Business';
    $Brand_unit = 'No. Of Unit';
}

if (isset($_POST['doctor_name']) && $_POST['search_term'] == 0 && $_POST['search_term'] != "total_business") {

    $brandSeriesData = array();
    $doctorName = $_POST['doctor_name'];
    $xAxisData = array();

    $m = 1;
    for ($i = 1; $i <= 12; $i++) {
        $m++;
        $monthData = array();
        $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
        $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

        $brandList = BusiProfile::docwise_business($doctorName, $m);
        if (isset($brandList)) {
            array_push($brandSeriesData, (float) $brandList);
        } else {
            array_push($brandSeriesData, 0);
        }
        array_push($xAxisData, $monthname);
    }

    $title = 'Doctor Wise Business';
}

if (isset($_POST['doctor_name']) && isset($_POST['search_term']) && $_POST['search_term'] == "total_business" && $_POST['doctor_name'] != 0) {

    $brandSeriesData = array();
    $doctorName = $_POST['doctor_name'];
    $xAxisData = array();

    $m = 1;
    for ($i = 1; $i <= 12; $i++) {
        $m++;
        $monthData = array();
        $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
        $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

        $brandList = BusiProfile::docwise_business($doctorName, $m);
        if (isset($brandList)) {
            array_push($brandSeriesData, (float) $brandList);
        } else {
            array_push($brandSeriesData, 0);
        }
        array_push($xAxisData, $monthname);
    }

    $title = 'Doctor Wise Business';
}


require_once("BMheader.php");
?>
<script>
    $(function () {
        $('#Linechart').highcharts({
            chart: {
                renderTo: 'Linechart',
                defaultSeriesType: 'spline',
                marginRight: 20,
                //marginBottom: 20
            },
            title: {
                text: 'Brandwise Business',
                x: -20 //center
            },
            xAxis: {
                categories: <?php echo json_encode($xAxisData); ?>
            },
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
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
                    text: 'Business'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
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
                    name: '<?php echo $Brand_unit; ?>',
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
    <form action="" method="post">
        <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">

            <select name="search_term" onchange="this.form.submit()" class="form-control">
                <?php
                if (isset($_POST['search_term'])) {
                    echo productList($_POST['search_term']);
                } else {
                    echo productList();
                }
                ?>

            </select>

        </div>

        <div class="col-lg-3 col-sm-4 col-md-4 col-xs-8">
            <select name="doctor_name" onchange="this.form.submit()" class="form-control">
                <option value="0">Select Doctor</option>
                <?php
                foreach ($employees as $employee):
                    $doctors = Doctor::find_all($employee->empid);
                    foreach ($doctors as $doctor):
                        ?>
                        <option value="<?php echo $doctor->docid; ?>" <?php
                        if (isset($_POST['doctor_name']) && $_POST['doctor_name'] == $doctor->docid) {
                            echo 'selected';
                        }
                        ?>><?php echo $doctor->name; ?></option>
                                <?php
                            endforeach;
                        endforeach;
                        ?>
            </select>
        </div>

    </form>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div id="Linechart" style="width: 100%; height: 100%;"></div>
    </div>
</div>

<?php require_once("BMfooter.php"); ?>