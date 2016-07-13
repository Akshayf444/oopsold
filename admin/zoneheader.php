<?php
if (!isset($_SESSION['name'])) {
    header("Location:logout.php");
     echo $_SESSION['name'];
}

// echo  $_SESSION['zone_id'];
?>
<!DOCTYPE html>
<html lang="en">
<?php // $zoneName = zone::find_by_zoneid($_SESSION['zone']);
// var_dump($zoneName);
//?>
     
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
                echo "Foresight";
            }
            ?>
        </title>


        <!-- Bootstrap Core CSS -->
        <link href="../css/bootstrap.css" rel="stylesheet">
        <script src="../js/jquery-1.11.0.js"></script>
        	<script src="../js/highcharts.js"></script>
        <!-- MetisMenu CSS -->
        <link href="../css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
        <link href="../css/jquery-ui.css" rel="stylesheet">



        <!-- Custom CSS -->
        <link href="../css/sb-admin-2.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

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
                    <a class="navbar-brand" href="zone_dashboard.php"><img src="../images/Logo.png" width="150px" height="100px" ></a>

                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-right top-nav">
                    <img src="../images/foresight.png" width="120px" height="50px" >
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><?php
//                            if (!empty($zoneName)) {
                                echo " " . $_SESSION['name']
//                            }
                            ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
                            </li>
                            <?php
                            if (isset($pageTitle) && $pageTitle === 'Profile') {
                                echo '<li><a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a></li>';
                            } else {
                                echo '<li><a href="#" data-toggle="modal" data-target=".logout"><i class="fa fa-fw fa-power-off"></i> Log Out</a></li>';
                            }
                            ?>
                        </ul>

                    </li>
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">

                        <ul class="nav" id="side-menu">
                            <li>
                                <a class="active" href="zone_dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>

                            <li>
                                <a href="zone_team.php"><i class="fa fa-group"></i> Your Team</a>
                            </li>

                            <li>
                                <a href="zone_alldoc.php"><i class="fa fa-list-alt"></i> View All Profiles</a>
                            </li>
<!--                            <li>
                                <a href="BMupComingBirthday.php"><i class="glyphicon glyphicon-calendar"></i> Upcoming Birthdays</a>
                            </li>
                            <li>
                                <a href="BMbrandWiseTrend.php"><i class="fa fa-bar-chart-o"></i> Brandwise Trend</a>
                            </li>-->
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>
            <div class="modal fade logout" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg largeModal">
                    <div class="col-lg-8 col-lg-push-2">
                        <div class="modal-content logoutModal">
                            <div class="modal-header huge" ><i class="fa fa-sign-out"></i> Log Out ?</div>
                            <div class=" modal-body"><p>Are you sure you want to log out?</p>
                                Press <span class="text-danger">No</span> if you want to continue work. Press <span class="text-success">Yes</span> to logout current user.</div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <a href="logout.php" class="btn btn-success">Yes</a>
                                        <a href="#" class="btn btn-danger" data-dismiss="modal" aria-label="Close">No</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="page-wrapper">