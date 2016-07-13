<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
}
require_once("../includes/initialize.php");


$Sm_list = SM::find();
$DoctorClass = array('A', 'B', 'A++', 'A+', 'Z');
foreach ($DoctorClass as $value) {
    $final_count = 0;
    $class = $value;
    $Count = BasicProfile::adminDrawPieChart($class);
    $final_count +=$Count;

    $dataString = "['" . $class . "'," . $final_count . "]";

    $myurl[] = $dataString;
}


//Service Graph
$servives = array('yes', 'no');
foreach ($servives as $service) {
    $final_count = 0;

    $Count = Services::admindrawPieChart(strtolower($service));
    $final_count +=$Count;

    $dataString = "['" . $service . "'," . $final_count . "]";
    $myurl1[] = $dataString;
}

//Buisness Profie Bar Graph
$xAxis_title = array('0-5000 <br/>(B) ', '5000-10000 <br/>(A)', '10000-15000 <br/>(A+)', '> 15000 <br/>(A++)');
$total = array('SUM(total) BETWEEN 0 AND 5000', 'SUM(total) BETWEEN 5001 AND 10000', 'SUM(total) BETWEEN 10001 AND 15000', 'SUM(total) > 15000');
foreach ($total as $value) {
    $final_count = 0;

    $Count = BusiProfile::adminBarGraph($value);
    $final_count += $Count;

    $myurl3[] = $final_count;
}

//Monthwise Business
$xAxisData = array();
$brandSeriesData = array();
$m = 1;
for ($i = 1; $i <= 12; $i++) {
    $m++;
    $monthData = array();

    $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
    $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

    $brand_busi_list = BusiProfile::overall_lastmonth_business($month);
    if (isset($brand_busi_list)) {
        array_push($monthData, $brand_busi_list);
    } else {
        array_push($monthData, 0);
    }
    $Brand_unit = 'Value';

    array_push($xAxisData, $monthname);
    array_push($brandSeriesData, array_sum($monthData));
}

$pageTitle = "Dashboard";

require_once("adminheader.php");

$xAxisData = array();
$Activitys = array();
$m = 1;
for ($i = 1; $i <= 12; $i++) {
    $m++;
    $monthData = array();

    $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
    $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

    $activityCount = Activity::count_all_by_month($month);
    array_push($monthData, $activityCount);


    array_push($xAxisData, $monthname);
    array_push($Activitys, array_sum($monthData));
}
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
                text: 'Service Provided By Doctors'
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
                text: 'Monthly Buisness'
            },
            xAxis: {
                categories: <?php echo json_encode($xAxis_title) ?>
            },
            yAxis: {
                allowDecimals: false,
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
                categories: <?php echo json_encode($xAxisData) ?>
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

    $(function () {
        $('#BarGraph2').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthwise Activities'
            },
            xAxis: {
                categories: <?php echo json_encode($xAxisData) ?>
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
                        <i class="fa fa-user-md fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"> 
                            <?php
                            $doctorCount = 0;

                            $doctorCount = Doctor::all();
                            echo $doctorCount;
                            ?></div>
                        <div>Total Doctors</div>
                    </div>
                </div>
            </div>
            <a href="ListDoctors.php">
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
                        <div class="huge"><?php
                            $profile_count = 0;
                            $profile_count += Doctor::profileCount_admin();
                            echo $profile_count;
                            ?></div>
                        <div>Completed Profile</div>
                    </div>
                </div>
            </div>
            <a href="ListDoctors.php?complete=true">
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

                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php
                            $BusiProfile = BusiProfile::overall_lastmonth_business();
                            echo $BusiProfile->SUM1;
                            ?></div>
                        <div>Last Month Business</div>
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
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-support fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo Activity::count_all(); ?></div>
                        <div>Total Activities</div>
                    </div>
                </div>
            </div>
            <a href="viewActivity.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Doctor Classification
            </div>
            <div class="panel-body">
                <div id="piechart" style=""></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Services
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
            <i class="fa fa-bar-chart-o fa-fw"></i> No. of Doctors By Business
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
<?php require_once("adminfooter.php");
?>