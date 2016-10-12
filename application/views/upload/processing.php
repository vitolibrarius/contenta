<script language="javascript" type="text/javascript">
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		$('a.rpc').click(function(e){
			e.preventDefault();
			$(".rpc_tools." + $(this).attr('data_key')).hide();
			$(".rpc_spinner." + $(this).attr('data_key')).show();
			var ajaxDiv = $("#ajaxDiv_" + $(this).attr('data_key'));

			var fig = $("#fig_" + $(this).attr('data_key')).addClass("blocked");
			var anchor = $("#a_" + $(this).attr('data_key')).removeAttr("href");

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
				img: '<?php echo Config::Web("/AdminUploadRepair/firstThumbnail/") ?>' + '/' + $(this).attr('data_key'),
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
<section>
	<div class="row">

<?php
	$runningJobs = $this->job_model->allForProcessor('UploadImport');
	$runningIndex = array();
	foreach( $runningJobs as $j ) {
		$h = (isset($j->guid) ? $j->guid : "-none-");
		$runningIndex[$h][] = $j;
	}
?>

<?php if ($this->active): ?>
<?php foreach ($this->active as $key => $value): ?>
<?php
	$running = (isset($runningIndex[$key]) && count($runningIndex[$key]) > 0);
?>
<div class="grid_3">
<figure class="card">
	<div class="figure_top <?php if ( $running == true ) { echo 'blocked'; } ?>" id="fig_<?php echo $key; ?>">
		<div class="figure_image">
			<?php if ( $running == false ) : ?>
				<a href="<?php echo Config::Web('/AdminUploadRepair/editUnprocessed', $key); ?>" id="a_<?php echo $key; ?>">
			<?php endif; ?>
			<img src="<?php echo Config::Web('/AdminUploadRepair/firstThumbnail', $key) ?>" class="thumbnail">
			<?php if ( $running == false ) : ?>
				</a>
			<?php endif; ?>
		</div>
		<div class="figure_details">
			<div class="figure_detail_top">
			</div>
			<div class="figure_detail_middle">
				<p class="status">
					<?php echo Localized::Get("Upload", (isset($value, $value['status']) ? $value['status'] : 'unknown')); ?>
				</p>
				<p class="file_size">
					<?php echo (isset($value, $value['size']) ? formatSizeUnits($value['size']) : 'Unknown'); ?>
				</p>
			</div>
			<div class="figure_detail_bottom">
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
			</div>
		</div>
	</div>

	<figcaption class="caption">
			<?php if ( $running == false ) : ?>
				<a href="<?php echo Config::Web('/AdminUploadRepair/editUnprocessed', $key); ?>" id="a_<?php echo $key; ?>">
			<?php endif; ?>
			<em><?php echo (isset($value, $value['name']) ? $value['name'] : $value['filename']); ?></em>
			<?php if ( $running == false ) : ?>
				</a>
			<?php endif; ?>
		<div id="ajaxDiv_<?php echo $key; ?>" ></div>
	</figcaption>
</figure>
</div>
<?php endforeach; ?>
<?php endif; ?>
	</div>
</section>
