<?php session_start(); if(!isset($_SESSION['employee'])){header("Location:login.php"); }
 require_once(dirname(__FILE__)."/includes/initialize.php");
?>
<form action="BrandWiseTrend.php" method="post">
<select name="brand" onchange="this.form.submit()">
<option value="">Select Brand</option>
<option value="brand1">Brand1</option>
<option value="brand2">Brand2</option>
<option value="brand3">Brand3</option>
<option value="brand4">Brand4</option>
<option value="brand5">Brand5</option>
<option value="brand6">Brand6</option>
<option value="brand7">Brand7</option>
<option value="brand8">Brand8</option>
</select>
</form>