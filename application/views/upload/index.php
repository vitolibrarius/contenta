<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminUploadRepair/index'); ?>"><?php echo $this->label( "RepairLink", "name" ); ?></a></li>
	</ul>
</div>

<form action="<?php echo Config::Web('/Upload/upload'); ?>" method="post" enctype="multipart/form-data">
	<fieldset>
	<legend>Upload Media</legend>
		<label for="mediaFile">Select File:</label>
		<input type="file" name="mediaFile" id="mediaFile"></input>

		<label for="submit"></label>
		<input type="submit" name="submit" Value="Submit"></input>
	</fieldset>
</form>
