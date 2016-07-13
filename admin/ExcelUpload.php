<?php session_start();    
if(!isset($_SESSION['admin'])){ header('Location: login.php');}
require_once("../includes/initialize.php");
$errors=array();
	$max_file_size = 10485760;
	if(isset($_POST['TM']) ) {
		if(isset($_FILES['file_upload1']) && !empty($_FILES['file_upload1'])){
		$photo = new Photograph();

		$photo->attach_file($_FILES['file_upload1']);
		if(file_exists($photo->filename)) {
			array_push($errors, "file already exist");
		}
		else{
			$flag ='TM';
			if($photo->save($flag)){
			array_push($errors, "Uploaded Succesfully.");
			}else{
				 $message = join("<br />", $photo->errors);
				array_push($errors, $message);
			}
		} 
	}
	}

		if(isset($_POST['BM'])) {
		if(isset($_FILES['file_upload2']) && !empty($_FILES['file_upload2'])){
		$photo = new Photograph();
		$photo->attach_file($_FILES['file_upload2']);
		if(file_exists($photo->filename)) {
			array_push($errors, "file already exist");
		}
		else{
			$flag ='BM';
			if($photo->save($flag)){
			array_push($errors, "Uploaded Succesfully.");}
			else{
				 $message = join("<br />", $photo->errors);
				array_push($errors, $message);
			}
		} 
		}
	}

		if(isset($_POST['SM'])) {
		if(isset($_FILES['file_upload3']) && !empty($_FILES['file_upload3'])){
		$photo = new Photograph();
		$photo->attach_file($_FILES['file_upload3']);
		if(file_exists($photo->filename)) {
			array_push($errors, "file already exist");
		}
		else{
			$flag ='SM';
			if($photo->save($flag)){
			array_push($errors, "Uploaded Succesfully.");}
			else{
				 $message = join("<br />", $photo->errors);
				array_push($errors, $message);
			}
		} 
		}
	}

	$pageTitle ="Excel Upload";

	  require_once("adminheader.php");
?>
 <div class="row">
             <div class="col-lg-12">
                   <h1 class="page-header">Excel Upload</h1>
             </div>
                <!-- /.col-lg-12 -->
</div>
<div class="row">
	<ul>
		<?php foreach($errors as $val){ ?>
        <li style="color:red;list-style-type:none;"><?php echo $val; ?></li>
    	<?php } ?>
	</ul>
</div>

<div class="row">
	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
	  <form action="ExcelUpload.php" enctype="multipart/form-data" method="POST">
	    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
	    <p>Upload TM File: <button type="file" name="file_upload1" class="btn btn-danger btn-xs"><i class="fa fa-file-excel-o"></i> Choose File</button></p>
	    <button type="submit" name="TM"  class="btn btn-primary"  /><i class="fa fa-upload"></i> Upload</button>
	  </form> 
	<hr/>
	    <form action="ExcelUpload.php" enctype="multipart/form-data" method="POST">
	    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
	    <p>Upload BM File: <button type="file" name="file_upload2" class="btn btn-danger btn-xs" ><i class="fa fa-file-excel-o"></i> Choose File</button></p>
	    <button type="submit" name="BM"  class="btn btn-primary"  /><i class="fa fa-upload"></i> Upload</button>
	  </form> 
	<hr/>
	    <form action="ExcelUpload.php" enctype="multipart/form-data" method="POST">
	    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
	    <p>Upload SM File: <button type="file" name="file_upload3" class="btn btn-danger btn-xs"><i class="fa fa-file-excel-o"></i> Choose File</button></p>
	    <button type="submit" name="SM"  class="btn btn-primary"  /><i class="fa fa-upload"></i> Upload</button>
	  </form>
	</div>
</div>
 <?php   require_once("adminfooter.php");?>