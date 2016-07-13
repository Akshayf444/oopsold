<?php
require_once("../includes/initialize.php");
$errors = array();
// Remember to give your form's submit tag a name="submit" attribute!
if (isset($_POST['submit'])) { // Form has been submitted.
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check database to see if username/password exist.
    $found_user = User::authenticate($username, $password);
    $found_manager = User::authenticate_zone($username, $password);

    if ($found_user) {
        session_start();
        $_SESSION['admin'] = "$username";
        //log_action('Login', "{$found_user->username} logged in.");
        redirect_to("DashBoard.php");
    } elseif ($found_manager) {
//        var_dump($found_manager);
        session_start();

        $_SESSION['zone_id'] = $found_manager->zone_id;
        $_SESSION['name'] = $found_manager->name;
        $_SESSION['zone_name'] = $found_manager->zone;

        //log_action('Login', "{$found_user->username} logged in.");
        redirect_to("zone_dashboard.php");
    } else {
        $message = "Username/password combination incorrect.";
        array_push($errors, $message);
        // username/password combo was not found in the database
    }
} else { // Form has not been submitted.
    $username = "";
    $password = "";
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Login</title>
        <script src="../js/jquery-1.11.0.js"></script>
        <!-- Bootstrap Core CSS -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="../css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="../css/sb-admin-2.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="../font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    </head>

    <body>

        <div class="container">
            <div class="row">

            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">

                    <div class="login-panel panel panel-default" id="loginPanel">
                        <ul style="list-style-type:none;">
                            <?php
                            if (isset($errors)) {
                                foreach ($errors as $value) {
                                    echo "<strong><li style='color:red;padding:5px'>" . $value . "</li></strong>";
                                }
                            }
                            ?>
                        </ul>
                        <div class="panel-heading">
                            <h3 class="panel-title">Please Sign In</h3>
                        </div>
                        <div class="panel-body">
                            <form action="login.php" method="post">
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Username" name="username"  autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                    </div>
                                    <!-- Change this to a button or input when using this as a form -->
                                    <button class="btn btn-lg btn-success btn-block" type="submit" name="submit" >Login</button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery Version 1.11.0 -->


        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="js/sb-admin-2.js"></script>

    </body>

</html>