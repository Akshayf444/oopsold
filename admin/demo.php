<?php require_once("../includes/initialize.php");?>
<h1>Welcome</h1><input id="myText" name="myText" type="text" />
<script language="javascript" type="text/javascript">
$(document).ready(function(){
		$('#myText').hide(); // on default, hide textbox
		$('#myCheckbox').click(function(){
		var checked_status = this.checked;
		if(checked_status == true) {
		$('#myText').show();
		}
  });
}); 
