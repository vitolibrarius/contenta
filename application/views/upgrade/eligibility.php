<?php $git = new utilities\Git(SYSTEM_PATH);
	$status = $git->status();
 ?>
<pre>
	<?php echo var_export($status, true); ?>
</pre>
