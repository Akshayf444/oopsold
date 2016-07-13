<?php
session_start();

if (!isset($_SESSION['employee'])) {
    header("Location:login.php");
}

$pageTitle = "index";
require_once(dirname(__FILE__) . "/includes/initialize.php");
$empid = $_SESSION['employee'];

//Class Graph
global $database;

$DoctorClass = array('A', 'B', 'A++', 'A+', 'Z');
foreach ($DoctorClass as $value) {
    $class = $value;
    $Count = BasicProfile::drawPieChart($empid, $class);
    $dataString = "['" . $class . "'," . $Count . "]";

    $myurl[] = $dataString;
}

//Service Graph

$service = 'Yes';
$Count = Services::drawPieChart($empid, strtolower($service));
$dataString = "['" . $service . "'," . $Count . "]";

$myurl1[] = $dataString;

$service = 'No';
$Count = Services::drawPieChart($empid, strtolower($service));
$dataString = "['" . $service . "'," . $Count . "]";

$myurl1[] = $dataString;

//Buisness Profie Bar Graph
$total = 'SUM(total) BETWEEN 0 AND 5000';
//$buisness = '500-1500';
$Count = BusiProfile::drawBarGraph($empid, $total);
$xAxis_title = array('0-5000 <br/>(B) ', '5000-10000 <br/>(A)', '10000-15000 <br/>(A+)', '> 15000 <br/>(A++)');

$myurl3[] = $Count;

$total = 'SUM(total) BETWEEN 5001 AND 10000';
//$buisness = '1500-2000';
$Count = BusiProfile::drawBarGraph($empid, $total);

$myurl3[] = $Count;

$total = 'SUM(total) BETWEEN 10001 AND 15000';
//$buisness = '2000-5000';
$Count = BusiProfile::drawBarGraph($empid, $total);

$myurl3[] = $Count;

$total = 'SUM(total) > 15000';
//$buisness = '> 5000';
$Count = BusiProfile::drawBarGraph($empid, $total);
//}

$myurl3[] = $Count;


//Monthwise Business
$xAxisData = array();
$brandSeriesData = array();
$m = 1;
for ($i = 1; $i <= 12; $i++) {
    $m++;
    $monthData = array();

    $monthname = date("M ", mktime(0, 0, 0, $m - 1, 0, date("Y", time()))); //Month Name
    $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

    $brand_busi_list = BusiProfile::Month_wise_Buisness($month, $empid);

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

$top_activities = Activity::find_topper();

require_once("layouts/TMheader.php");
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dashboard</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<script src="js/highcharts.js"></script>
<script src="js/exporting.js"></script>
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
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user-md fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $Doctor = Doctor::count_all($empid); ?></div>
                        <div>Total Doctors</div>
                    </div>
                </div>
            </div>
            <a href="listAllDoctors.php">
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
                        <div class="huge"><?php echo Doctor::profileCount_empwise($empid) ?></div>
                        <div>Completed Profile</div>
                    </div>
                </div>
            </div>
            <a href="listAllDoctors.php?complete=true">
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
                            $BusiProfile = BusiProfile::lastMonthBuisness($empid);
                            echo $BusiProfile->SUM1;
                            ?></div>
                        <div>Last Month Business</div>
                    </div>
                </div>
            </div>
            <a href="listBusinessProfile.php">
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
                        <div class="huge"><?php
                            $Activity = Activity::count_by_empid($empid);
                            echo $Activity;
                            ?></div>
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
                        <div class="col-md-2 col-xs-12 column productbox">
                            <img src="<?php
                            $path1 = 'files/' . $employee->profile_photo;
                            $path2 = "files/Default.png";
                            echo isset($employee->profile_photo) && $employee->profile_photo != '' && file_exists($path1) ? $path1 : $path2;
                            ?>" class="img-responsive">
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
                <i class="fa fa-bar-chart-o fa-fw"></i> Service
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

<?php
$xAxisData1 = array();
$Activitys = array();
$m = 1;
for ($i = 1; $i <= 12; $i++) {
    $m++;
    $monthData = array();

    $monthname = date('M', mktime(0, 0, 0, $m, 1, date('Y'))); //Month Name
    $month = date('m', mktime(0, 0, 0, $m, 1, date('Y'))); // Month value

    $activityCount = Activity::count_for_month($empid, $month);
    array_push($monthData, $activityCount);


    array_push($xAxisData1, $monthname);
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
                text: 'Service Provided to Doctors'
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
                text: 'Last Month Business'
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
<?php require_once("layouts/TMfooter.php"); ?>