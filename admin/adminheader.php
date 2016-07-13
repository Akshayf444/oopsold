<!DOCTYPE html>
<html lang="en">
    <?php $empName = User::find_by_emailid($_SESSION['admin']); ?>
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php
            if (isset($pageTitle)) {
                echo $pageTitle;
            } else {
                echo 'Foresight Admin';
            }
            ?></title>


        <link href="../css/bootstrap.css" rel="stylesheet">
        <script src="../js/jquery-1.11.0.js"></script>

        <link href="../css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
        <link href="../css/jquery-ui.css" rel="stylesheet">

        <link href="../css/sb-admin-2.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">

        <link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="../js/ajaxLoader.js" type="text/javascript"></script>
    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="DashBoard.php">Home</a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-right top-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php
                            if (!empty($empName)) {
                                echo " " . $empName->name;
                            }
                            ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation" style="  margin-top: 51px;">
                    <div class="sidebar-nav navbar-collapse">

                        <ul class="nav" id="side-menu">
                            <li>
                                <a class="active" href="DashBoard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <li><a href="#"><i class="fa fa-book"></i> Masters<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="SM_Master.php">SM Master</a>
                                    </li>
                                    <li>
                                        <a href="BM_Master.php">BM Master</a>
                                    </li>
                                    <li>
                                        <a href="TM_Master.php">TM Master</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#"><i class="glyphicon glyphicon-wrench "></i> Manage Employee<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="index.php">Replace Employee</a>
                                    </li>
                                    <li>
                                        <!--                                        <a href="AddAcademicProfile.php">Replace</a>-->
                                    </li>
                                    <li>
                                        <a href="reportingChange.php">Reporting Change</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
                                <a href="#"><i class="glyphicon glyphicon-wrench "></i> Reports<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="ZoneWiseDoctors.php">AreaWise Report </a>
                                    </li>
                                    <li>
                                        <a href="SMwise_report.php">SM wise Report</a>
                                    </li>
                                </ul>
                            </li>
                            <!--<li><a href="addEmployee.php">Add Employee Details</a></li> -->

                            <!--                        <li><a href="replaceEmployee.php">Replace Employee</a></li> -->
<!--                            <li><a href="ExcelUpload.php">Excel Upload</a></li> -->
<!--                            <li><a href="ExcelReport.php">Excel Report</a></li> -->
                            <li><a href="TMreport.php">TM Doctor</a></li>   
<!--                            <li><a href="SendSms.php">Send Sms</a></li>-->

                            <li><a href="lockManager.php">Lock Manager</a></li>
                            <li><a href="SmsCount.php">SMS Count</a></li>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <div id="page-wrapper">