<?php
require_once(dirname(__FILE__) . "/includes/initialize.php");
$errors = array();
session_start();
if (isset($_POST['submit'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $found_user = Employee::authenticate($username, $password);

    if ($found_user != false) {
        $_SESSION['cipla_empid'] = $found_user->cipla_empid;
        $_SESSION['employee'] = $found_user->empid;
        $_SESSION['team'] = $found_user->team;
        //log_action('Login', "{$found_user->username} logged in.");
        redirect_to("index.php");
    } else {
        $found_bm = BM::authenticate($username, $password);
        if ($found_bm != false) {
            $_SESSION['BM'] = "$username";
            $_SESSION['team'] = $found_bm->team;
            redirect_to("BM/BMindex.php");
        } else {
            $found_sm = SM::authenticate($username, $password);
            if ($found_sm != false) {
                $_SESSION['SM'] = "$username";
                //$_SESSION['team'] = $found_sm->team;
                redirect_to("SM/SMindex.php");
            } else {
                $_SESSION['message'] = "<div id='mini-notification'>
                                <p style = 'color:red;font-weight:bold'>Incorrect Username/password combination .</p>
                            </div>";
            }
        }
    }
} else { // Form has not been submitted.
    $username = "";
    $password = "";
}

if (isset($_POST['forgot_password'])) {
    $empid = $_POST['empid'];
    $tm = Employee::cipla_empid($empid);
    if (!empty($tm)) {
        sendsms($tm->mobile, "Your Password is ".$tm->password);
    } else {
        $found_bm = BM::find_by_bmid($empid);
        if ($found_bm != false) {
            sendsms($found_bm->mobile, "Your Password is ".$found_bm->password);
        } else {
            $found_sm = SM::find_by_smid($empid);
            if ($found_sm != false) {
                sendsms($found_sm->mobile, "Your Password is ".$found_sm->password);
            }
        }
    }
    
    $_SESSION['message'] = "<div id='mini-notification'>
                                <p style = 'color:red;font-weight:bold'>Your Password is sent to your mobile no .</p>
                            </div>";
    
   // redirect_to('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="images/favicon.png" type="image/png">

        <title>Foresight</title>
        <link href="css/site.css" rel="stylesheet" type="text/css"/>
        <link href="css/style.default.css" rel="stylesheet">
        <script src="js/jquery.js" type="text/javascript"></script>
        <script src="js/miniNotification.js" type="text/javascript"></script>
        <script src="js/bootstrap.js" type="text/javascript"></script>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5.js"></script>
        <script src="js/respond.js"></script>
        <![endif]-->
    </head>

    <body class="signin">
        <header>
            <?php
            if (isset($_SESSION['message']))
                echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </header>
        <section>
            <div class="signinpanel">
                <div class="pull-right">Helpline No : <span class="helpline">022-65657701</span><br>From 10 am - 6 pm</div>
                <h2 class="hidden-xs" style="margin-left: -100px"><strong>Welcome to <img src="images/foresight.png" width="25%" height="25%" ><head>

                        </head></strong></h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="signin-info">
                            <div class="logopanel">
                                <img src="images/Logo.png" width="100%">
                            </div>
                        </div><!-- signin0-info -->

                    </div><!-- col-sm-7 -->
                    <div class="col-md-1"></div>
                    <div class="col-md-5">

                        <form method="post" action="">
                            <h4 class="nomargin">Sign In</h4>
                            <p class="mt5 mb20">Login to access your account.</p>

                            <input type="text" class="form-control uname" placeholder="Username" name="username"/>
                            <input type="password" class="form-control pword" placeholder="Password" name="password" />
        <!--                    <a href="#"><small>Forgot Your Password?</small></a>-->
                            <button class="btn btn-success btn-block" type="submit" name="submit" style="background: #2A5567">Sign In</button>
                            <a href="#" data-toggle="modal" data-target="#forgot">Forgot Password</a>
                        </form>
                    </div><!-- col-sm-5 -->

                </div><!-- row -->
                <hr style="margin-top: 7em">
                <div >
                    <div class="pull-left">
                        &copy; 2015. All Rights Reserved. 
                    </div>
                    <div class="pull-right">
                        Powered By: <a href="http://techvertica.com/" target="_blank">Techvertica</a>
                    </div>
                </div>

            </div><!-- signin -->

        </section>
        <div class="modal fade " tabindex="-1" role="dialog" aria-labelledby="ModalLabel" id="forgot" aria-hidden="true">
            <div class="modal-dialog " >
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="exampleModalLabel">Forgot Password</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post"   name="form" class="form"   enctype="multipart/form-data" role="form" action="#" onsubmit="return (validate());">
                            <div class=" form-group">
                                <input type="text" class="form-control" name="empid"    placeholder="Employee Id"  >
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit"  name="forgot_password" value="Get Password" class="btn btn-primary">Send Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(function () {
                $('#mini-notification').miniNotification({closeButton: true, closeButtonText: 'x'});
                function blinker() {
                    $('.helpline').fadeOut(500);
                    $('.helpline').fadeIn(500);
                }

                setInterval(blinker, 1000);
            });
        </script>
    </body>
</html>
