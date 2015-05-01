<script language="javascript" type="text/javascript">
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		$('a.rpc').click(function(e){
			e.preventDefault();
			$(".rpc_tools." + $(this).attr('data_key')).hide();
			$(".rpc_spinner." + $(this).attr('data_key')).show();
			var ajaxDiv = $("#ajaxDiv_" + $(this).attr('data_key'));

			$.ajax({
				type: "GET",
				url: $(this).attr('data_action'),
				dataType: "text",
				success: function(msg){
					ajaxDiv.html(msg);
				}
			});

		});

		var list = $('a.confirm');
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
				img: '<?php echo Config::Web("/AdminUploadRepair/firstThumbnail/") ?>' + $(this).attr('data_key'),
				description: '<?php echo $this->label( "index", "DeleteDescription"); ?> <br /><em>' + $(this).attr('data_filename') + '</em>',
				confirm: '<?php echo $this->label( "index", "DeleteConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
				action: $(this).attr('data_action')
			});
			e.preventDefault();
		});
	});
</script>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/Upload/index'); ?>"><?php echo $this->label( "UploadLink", "name" ); ?></a></li>

	<?php $min = 0; $max = $this->pageCount;
	if ($this->pageCurrent > 5) {
		$min = $this->pageCurrent - 5;
	}
	if ($max - $this->pageCurrent > 5 ) {
		$max = $this->pageCurrent + 5;
	}
	if ( $min > 0 ) {
		echo '<li><a href="'. Config::Web('/AdminUploadRepair/index/0') .'">First</a></li><li>....</li>';
	}
	for ($x = $min; $x < $max; $x++) {
		if ( $x == $this->pageCurrent ) {
			echo '<li class="current">';
		}
		else {
			echo '<li>';
		}
		echo '<a href="'. Config::Web('/AdminUploadRepair/index', $x) . '">' . $x . '</a></li>';
	}
	if ( $max < $this->pageCount -1 ) {
		echo '<li>....</li><li><a href="'. Config::Web('/AdminUploadRepair/index', ($this->pageCount -1)) . '">Last</a></li>';
	}
	?>
	</ul>
</div>

<div class="mediaData">
	<table>
		<tr>
			<th></th>
			<th>Type</th>
			<th>Name</th>
			<th>Size</th>
			<th></th>
		</tr>
	<?php if ($this->active): ?>
	<?php foreach ($this->active as $key => $value): ?>
		<?php
			$runningJobs = $this->job_model->allForProcessorGUID('UploadImport', $key );
			$running = ( is_array($runningJobs) && count($runningJobs) > 0 );
		?>
		<tr <?php if ( $running == true ) { echo 'class="blocked"'; } ?> >
			<td>
				<?php if ( $running == false ) : ?>
					<a href="<?php echo Config::Web('/AdminUploadRepair/editUnprocessed', $key); ?>">
				<?php endif; ?>
				<img src="<?php echo Config::Web('/AdminUploadRepair/firstThumbnail', $key) ?>" class="thumbnail">
				<?php if ( $running == false ) : ?>
					</a>
				<?php endif; ?>
			</td>
			<td><?php echo (isset($value, $value['filename']) ? file_ext($value['filename']) : 'Unknown'); ?></td>
			<td>
				<h3 class="path"><?php echo (isset($value, $value['name']) ? $value['name'] : $value['filename']); ?></h3>
				<a href="#" limit="5" name="<?php echo $key; ?>" class="ajaxLogs">Logs</a>
				<div id="ajaxDiv_<?php echo $key; ?>" ></div>
			</td>
			<td><?php echo (isset($value, $value['size']) ? formatSizeUnits($value['size']) : 'Unknown'); ?></td>
			<td>
				<?php
					$spinDisplay = "none";
					$toolsDisplay = "inline";
					if ( $running == true ) {
						$spinDisplay = "inline";
						$toolsDisplay = "none";
					}
				?>

				<div class="rpc_tools <?php echo $key; ?>" style="display:<?php echo $toolsDisplay; ?>;">
					<a href="#" class="rpc" data_action="<?php echo Config::Web('/AdminUploadRepair/reprocess', $key); ?>" data_key="<?php echo $key; ?>" alt="Retry">
						<span class="icon retry" />
					</a>

					<a href="#" class="confirm"
						data_action="<?php echo Config::Web('/AdminUploadRepair/deleteUnprocessed', $key); ?>"
						data_key="<?php echo $key; ?>"
						data_filename="<?php echo (isset($value, $value['name']) ? $value['name'] : $value['filename']); ?>"
						alt="Delete">
						<span class="icon trash" />
					</a>
				</div>
				<img class="rpc_spinner <?php echo $key; ?>" style="display:<?php echo $spinDisplay; ?>;"
					src="<?php echo Config::Web('/public/select2-spinner.gif'); ?>" />
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</table>
</div>
