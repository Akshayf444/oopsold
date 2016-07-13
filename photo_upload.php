<?php require_once(dirname(__FILE__)."/includes/initialize.php");
	$max_file_size = 1048576;   // expressed in bytes
	                            //     10240 =  10 KB
	                            //    102400 = 100 KB
	                            //   1048576 =   1 MB
	                            //  10485760 =  10 MB

	if(isset($_POST['submit'])) {
		$photo = new Photograph();
		$photo->attach_file($_FILES['file_upload']);
		if($photo->create()) {
			// Success
			echo "Uploaded";
		} else {
			
		}
	}
	
?>

<a href="index.php">Back</a>
	<h2>File  Upload</h2>

  <form action="photo_upload.php" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
    <p><input type="file" name="file_upload" /></p>
    <input type="submit" name="submit" value="Upload" />
  </form> 