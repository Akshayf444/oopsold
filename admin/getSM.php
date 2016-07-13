<?php 	if(isset($_POST['search_term']) && ($_POST['search_term'] == 'BM')){?>
<select  disabled id="change" class="form-control">
    <option value='SM'>SM</option>
</select>
<?php }
		if(isset($_POST['search_term']) && ($_POST['search_term'] == 'TM')){   ?>
<select  disabled id="change" class="form-control">
    <option value='BM'>BM</option>
</select>
<?php } ?>