<?php session_start();    
if(!isset($_SESSION['admin'])){ header('Location: login.php');}
require_once("../includes/initialize.php"); 
$pageTitle="SMS Count";

  require_once("adminheader.php");
?>
 <div class="row">
             <div class="col-lg-12">
                   <h1 class="page-header">Manage Employee</h1>
             </div>
                <!-- /.col-lg-12 -->
</div>
<div class="row">
            <div  style="" class="col-lg-6 col-sm-6 col-md-6 col-xs-6 table-responsive">
	<table class="table table-bordered">
		<tr>
		    <th>Month</th>
		    <th>Count</th>
		</tr>
	<?php for ($i=2; $i <= date("m",time())+1 ; $i++) { ?>
		<tr>
			<td><?php echo date("M Y",mktime(0,0,0,$i,0,date("Y",time()))); ?></td>
			<td><?php $count = SMS::count_per_month($i-1); echo $count; ?></td>	    
      	</tr>	
 	<?php	}?>	    
 	</div> 
 </div> 	
<?php  require_once("adminfooter.php");