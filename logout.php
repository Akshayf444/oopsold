<?php
session_start();
$_SESSION['employee'] = null;
unset($_SESSION['employee']);
session_unset();
session_destroy();
echo '<script>window.location = "login.php";</script> ';
?>