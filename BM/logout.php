<?php
session_start();
session_destroy();

$_SESSION['doctor'] = null;
$_SESSION['BM'] = null;
header("Location:../login.php");
?>