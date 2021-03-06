<?php
session_start();
if (!isset($_SESSION['SM'])) {
    header("Location:../login.php");
}
require_once("../includes/initialize.php");
$sm_empid = $_SESSION['SM'];
$employees = Employee::find_by_smid($sm_empid);

$DoctorClass = array('A', 'B', 'A++', 'A+', 'Z');
foreach ($DoctorClass as $value) {
    $class = $value;
    $Count = BasicProfile::SMdrawPieChart($_SESSION['SM'], $class);
    $dataString = "['" . $class . "'," . $Count . "]";

    $myurl[] = $dataString;
}

//Service Graph

$service = 'Yes';
$Count = Services::SMdrawPieChart($_SESSION['SM'], strtolower($service));
$dataString = "['" . $service . "'," . $Count . "]";

$myurl1[] = $dataString;

$service = 'No';
$Count = Services::SMdrawPieChart($_SESSION['SM'], strtolower($service));
$dataString = "['" . $service . "'," . $Count . "]";

$myurl1[] = $dataString;

$total = array('SUM(total) BETWEEN 0 AND 5000', 'SUM(total) BETWEEN 5001 AND 10000', 'SUM(total) BETWEEN 10001 AND 15000', 'SUM(total) > 15000');
$xAxis_title = array('0-5000 <br/>(B) ', '5000-10000 <br/>(A)', '10000-15000 <br/>(A+)', '> 15000 <br/>(A++)');

foreach ($total as $value) {
    $Count = BusiProfile::SMdrawBarGraph($_SESSION['SM'], $value);
    $myurl3[] = $Count;
}


$pageTitle = "Index";

$sm_empid = $_SESSION['SM'];
$BMcount = BM::count_all($sm_empid);
$doctorCount = SM::count_all_doctors($sm_empid);
$empcount = BM::count_all_employees($sm_empid);
//$totalCount = SM::totalProfileCount($sm_empid);
//Monthwise Business
$xAxisData = array();
$brandSeriesData = array();
$m = 1;
for ($i = 1; $i <= 12; $i++) {
    $m++;
    $monthData = array();

    $monthname = date("M ", mktime(0, 0, 0, $m - 1, 0, date("Y", time()))); //Month Name
    $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value


    $brand_busi_list = 0;
    foreach ($employees as $employee) {
        $brand_busi_list += BusiProfile::Month_wise_Buisness($month, $employee->empid);
    }


    if (isset($brand_busi_list)) {
        array_push($monthData, $brand_busi_list);
    } else {
        array_push($monthData, 0);
    }
    $Brand_unit = 'Value';

    array_push($xAxisData, $monthname);
    array_push($brandSeriesData, array_sum($monthData));
}

$previous_date = date('Y-m-d', strtotime("first day of last month"));

$top_activities = Activity::find_topper($previous_date);

require_once("SMheader.php");
?>
<script>
    $(function () {
        $('#piechart').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 1, //null,
                plotShadow: false
            },
            title: {
                text: 'Doctor Class'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Class',
                    data: [
<?php echo join(',', $myurl) . ","; ?>
                    ]
                }]
        });
    });
</script>

<script>
    $(function () {
        $('#piechart1').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 1, //null,
                plotShadow: false
            },
            title: {
                text: 'Service Provided To Doctors'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Service',
                    data: [
<?php echo join(',', $myurl1) . ","; ?>
                    ]
                }]
        });
    });

    $(function () {
        $('#BarGraph').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthly Business'
            },
            xAxis: {
                categories: <?php echo json_encode($xAxis_title) ?>
            },
            yAxis: {
                title: {
                    text: 'No of doctors'
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
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                    name: 'Doctors',
                    data: [<?php echo join(",", $myurl3); ?>]
                }]
        });
    });

    $(function () {
        $('#BarGraph1').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthwise Business'
            },
            xAxis: {
                categories: <?php
$temp = array_shift($xAxisData);
array_push($xAxisData, $temp);
echo json_encode($xAxisData)
?>
            },
            yAxis: {
                allowDecimals: false,
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
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                    name: 'Value',
                    data: <?php echo json_encode($brandSeriesData); ?>
                }]
        });
    });

</script>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dashboard</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-comments fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"> <?php echo $empcount; ?></div>
                        <div>Total Employees</div>
                    </div>
                </div>
            </div>
            <a href="SMviewTeam.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-tasks fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"> <?php echo $doctorCount; ?></div>
                        <div>Total Doctors</div>
                    </div>
                </div>
            </div>
            <a href="SMviewAllDoctorsProfile.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-shopping-cart fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo Doctor::profileCount_smwise($sm_empid) ?></div>
                        <div>Profiles Completed </div>
                    </div>
                </div>
            </div>
            <a href="SMviewAllDoctorsProfile.php?complete=yes">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-support fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $BMcount; ?></div>
                        <div>Total BM</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<style>
    .productbox {
        background-color:#ffffff;
        padding:10px;
        margin-bottom:10px;
        margin-right: 5px;
        -webkit-box-shadow: 0 8px 6px -6px  #999;
        -moz-box-shadow: 0 8px 6px -6px  #999;
        box-shadow: 0 8px 6px -6px #999;
    }

    .producttitle {
        font-weight:bold;
        padding:5px 0 5px 0;
    }

    .productprice {
        border-top:1px solid #dadada;
        padding-top:5px;
    }

    .pricetext {
        font-weight:bold;
    }

    .img-responsive{
        height: 135px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Toppers List 
            </div>
            <div class="panel-body">

                <?php
                if (!empty($top_activities)) {
                    foreach ($top_activities as $activity) {
                        $employee = Employee::find_by_empid($activity->empid);
                        ?>
                        <div class="col-md-2 column productbox">
                            <img src="<?php echo isset($employee->profile_photo) && $employee->profile_photo != '' ? '../files/' . $employee->profile_photo : '../files/Default.png' ?>" class="img-responsive">
                            <div class="producttitle"><?php echo $employee->name ?></div>
                            <div class="producttitle"><?php echo $employee->HQ . " , " . $employee->state; ?></div>
                            <div class="productprice"><div class="pull-right"><a href="#" class="btn btn-danger btn-xs" role="button"><?php echo $activity->Activity_count; ?></a></div><div class="pricetext">Activity Count</div></div>
                        </div>

                        <?php
                    }
                }
                ?>

            </div>
        </div>
    </div>
</div>
<?php
$xAxisData1 = array();
$Activitys = array();
$m = 1;
for ($i = 1; $i <= 12; $i++) {
    $m++;
    $monthData = array();

    $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
    $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value
    $activityCount = 0;
    foreach ($employees as $employee) {
        $activityCount += Activity::count_for_month($employee->empid, $month);
    }

    array_push($monthData, $activityCount);
    array_push($xAxisData1, $monthname);
    array_push($Activitys, array_sum($monthData));
}
?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Charts
            </div>
            <div class="panel-body">
                <div id="piechart" style=""></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Charts
            </div>
            <div class="panel-body">
                <div id="piechart1" style=""></div>
            </div>
        </div>
    </div>
</div>
<div class="row" >      
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bar-chart-o fa-fw"></i> Charts
        </div>
        <div class="panel-body">
            <div id="BarGraph" style="width: 100%; height: 400px; float:left"></div>
        </div>
    </div>
</div>
<div class="row" >      
    <div class="col-lg-6" >      
        <div class="panel panel-default ">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Monthwise Business
            </div>
            <div class="panel-body">
                <div id="BarGraph1" style="width: 100%; height: 400px; float:left"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6" >  
        <div class="panel panel-default ">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Activities
            </div>
            <div class="panel-body">
                <div id="BarGraph2" style="width: 100%; height: 400px; float:left"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#BarGraph2').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthwise Activities'
            },
            xAxis: {
                categories: <?php echo json_encode($xAxisData1) ?>
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'No. Of Activities '
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
            credits: {
                enabled: false,
                text: 'Techvertica.com',
                href: 'http://www.techvertica.com'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                    name: 'Unit',
                    data: <?php echo json_encode($Activitys); ?>
                }]
        });
    });
</script>
<?php
require_once("SMfooter.php");
