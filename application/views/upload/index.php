<form action="<?php echo Config::Web('/Upload/upload'); ?>" method="post" enctype="multipart/form-data">
	<fieldset>
	<legend>Upload Media</legend>
		<label for="mediaFile">Select File:</label>
		<input type="file" name="mediaFile" id="mediaFile"></input>

		<label for="submit"></label>
		<input type="submit" name="submit" Value="Submit"></input>
	</fieldset>
</form>
