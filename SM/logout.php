<?php

session_start();
session_destroy();

$_SESSION['doctor'] = null;
$_SESSION['SM'] = null;


header("Location:../login.php");
?>