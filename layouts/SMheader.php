<!DOCTYPE html>
<html lang="en">
    <?php $empName = SM::find_by_smid($_SESSION['SM']); ?>
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php
            if (isset($pageTitle)) {
                echo $pageTitle;
            }
            ?></title>


        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/jquery-1.11.0.js"></script>
        <!-- MetisMenu CSS -->
        <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
        <link href="css/jquery-ui.css" rel="stylesheet">

        <!-- Timeline CSS -->
        <link href="css/plugins/timeline.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/sb-admin-2.css" rel="stylesheet">
        <link href="css/main.css" rel="stylesheet">
        <link href="css/responsiveTable.css" rel="stylesheet" type="text/css"/>
        <!-- Morris Charts CSS -->
        <link href="css/plugins/morris.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="js/jsapi.js"></script>
        <!--[if lt IE 9]>
        <script src="js/html5.js"></script>
        <script src="js/respond.js"></script>
        <![endif]-->
    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header" style="height: 100px">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><img src="./images/Logo.png" width="150px" height="100px" ></a>

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

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">

                        <ul class="nav" id="side-menu">
                            <li>
                                <a class="active" href="SMindex.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>

                            <li>
                                <a href="SMviewTeam.php"><i class="fa fa-group"></i> Your Team</a>
                            </li>

                            <li>
                                <a href="SMviewAllDoctorsProfile.php"><i class="fa fa-list-alt"></i> View All Profiles</a>
                            </li>
                            <li>
                                <a href="SMupComingBirthday.php"><i class="glyphicon glyphicon-calendar"></i> Upcoming Birthdays</a>
                            </li>
                            <li>
                                <a href="SMbrandWiseTrend.php"><i class="fa fa-bar-chart-o"></i> Brandwise Trend</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <div id="page-wrapper">