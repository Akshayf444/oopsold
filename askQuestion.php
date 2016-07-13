<?php
session_start();
if (!isset($_SESSION['employee'])) {
    header("Location : logout.php");
}
require_once(dirname(__FILE__) . "/includes/initialize.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta http-equiv="pragma" content="no-cache" />
        <title><?php
            if (isset($pageTitle)) {
                echo $pageTitle;
            } else {
                echo "Foresight";
            }
            ?>
        </title>

        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/main.css" rel="stylesheet">

        <script src="js/jquery-1.11.0.js"></script>
        <!-- MetisMenu CSS -->
        <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
        <link href="css/jquery-ui.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/sb-admin-2.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- All JS -->
        <script src="js/jquery-ui.js"></script>

        <script src="js/bootstrap.min.js"></script>
        <!--[if lt IE 9]>
            <script src="js/html5.js"></script>
            <script src="js/respond.js"></script>
        <![endif]-->
    </head>
    <body>
        <div id="wrapper">
            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0" >
                <div class="navbar-header" style="height: 100px" >
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><img src="./images/Logo.png" width="160px" height="95px" ></a>

                </div>
                <div style="float:left ;top:5%;right:15%;width: 70%">
                    <?php
                    $empid = $_SESSION['employee'];
                    $BirthdayFlash = Doctor::showBirthDayFlash($empid);
                    if (isset($BirthdayFlash)) {
                        echo $BirthdayFlash;
                    }
                    ?>
                </div>
                <ul class="nav navbar-right top-nav">
                    <li class="dropdown">
                        <img src="images/foresight.png" width="160px" height="60px" >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php
                            $empName = Employee::find_by_empid($_SESSION['employee']);
                            if (!empty($empName)) {
                                echo " " . $empName->name;
                            }
                            ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
                            </li>
                            <li>
                                <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                            </li>
                        </ul>

                    </li>
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">

                        <ul class="nav" id="side-menu">
                            <li class="sidebar-search">
                                <div class="input-group custom-search-form">
                                    <form action="Search.php" method="post" name="form" >
                                        <p><input type="text" class="form-control" placeholder="Search..." name="name" >

                                        </p>
                                    </form>
                                </div>
                                <!-- /input-group -->
                            </li>
                            <li>
                                <a class="active" href="index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-user-md "></i> Doctor<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="addDoctor.php"><i class="glyphicon glyphicon-plus"></i> Add Doctor Details</a>
                                    </li>
                                    <li>
                                        <a href="listAllDoctors.php"><i class="fa fa-list-alt"></i> List All Doctors</a>
                                    </li>

                                </ul>
                            </li>
                            <li>
                                <a href="#"><i class="glyphicon glyphicon-wrench "></i> Manage Doctor Profiles<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="AddProfile.php">Add Basic Profile</a>
                                    </li>
                                    <li>
                                        <a href="AddAcademicProfile.php">Add Academic Profile</a>
                                    </li>
                                    <li>
                                        <a href="AddServices.php">Add Service Profile</a>
                                    </li>

                                    <li>
                                        <a href="AddCompetitor.php">Add Competitor Details</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-suitcase"></i> Monthly Business<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="AddBusinessProfile.php">Add Business Profile</a>
                                    </li>
                                    <li>
                                        <a href="listBusinessProfile.php"></i> Last Month Business</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#"><i class="glyphicon glyphicon-wrench "></i> Activity<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="AddActivityDetails.php"><i class="glyphicon glyphicon-plus"></i> Add Activity Details</a>
                                    </li>
                                    <li>
                                        <a href="viewActivity.php"><i class="fa fa-desktop"></i> View Activity Details</a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="#"><i class="glyphicon glyphicon-wrench "></i> Product Priority<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="addPriority.php"><i class="glyphicon glyphicon-plus"></i> Add Product Priority</a>
                                    </li>
                                    <li>
                                        <a href="viewPriority.php"><i class="fa fa-desktop"></i> View Product Priority</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="upComingBirthday.php"><i class="glyphicon glyphicon-calendar"></i> Upcoming Birthdays</a>
                            </li>
                            <li>
                                <a href="BrandWiseTrend.php"><i class="fa fa-bar-chart-o"></i> Brandwise Trend</a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-sitemap fa-fw"></i> Planning<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="#">Monthly Planning <span class="fa arrow"></span></a>
                                        <ul class="nav nav-third-level">
                                            <li>
                                                <a href="add_plan_monthly.php">Add Planing</a>
                                            </li>
                                            <li>
                                                <a href="view_plan_monthly.php">View Planning</a>
                                            </li>
                                        </ul>
                                        <!-- /.nav-third-level -->
                                    </li> 
                                </ul>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="#">Daily Planning <span class="fa arrow"></span></a>
                                        <ul class="nav nav-third-level">
                                            <li>
                                                <a href="add_plan_daily.php">Add Planing</a>
                                            </li>
                                            <!--                                            <li>
                                                                                            <a href="view_daily_plan.php">View Planning</a>
                                                                                        </li>-->
                                        </ul>
                                        <!-- /.nav-third-level -->
                                    </li> 
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <!--                            <li>
                                                            <a href="askQuestion.php"><i class="fa fa-comment"></i> Ask</a>
                                                        </li>-->
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>
            <div id="page-wrapper">

                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Ask Question</h1>
                    </div>      <!-- /.col-lg-12 -->
                </div>
                <div class="row" >
                    <div class="col-lg-11 answerlist" >
                        <?php require_once(dirname(__FILE__) . "/askQuestionTemplate.php"); ?>
                    </div>      
                </div>
                <?php require_once(dirname(__FILE__) . "/layouts/TMfooter.php");
                ?>