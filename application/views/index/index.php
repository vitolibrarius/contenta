<section>

<?php foreach($this->queues as $key => $queue) : ?>
<div class="group">
	<div class="row">
		<div class="grid_1">
			<?php $src = $queue->source(); if ( $src != false) : ?>
			<img class="thumbnail cbz" src="<?php echo Config::Web( "Image", "thumbnail", $src->tableName(), $src->pkValue()); ?>" />
			<?php endif; ?>
		</div>
		<div class="grid_7">
			<h2 style="display: inline">
				<a href="#" class="queued" data-queue_id="<?php echo $queue->id; ?>">
					<?php $publish = $src->publisher(); if ( $publish != false) : ?>
						<img src="<?php echo Config::Web( "Image", "icon", 'publisher', $publish->pkValue()); ?>" class="icon publisher" />
					<?php endif; ?>
					<?php echo $queue->displayName(); ?>
				</a>
			</h2>
			<p><em><?php echo $src->displayDescription(); ?></em></p>
		</div>
		<div class="grid_2">
			<progress max="<?php echo $queue->pub_count; ?>" value="<?php echo (isset($queue->pub_read)?$queue->pub_read:0);?>"></progress>
			<div style="text-align: center;"><?php echo (isset($queue->pub_read)?$queue->pub_read:0); ?>
				/ <?php echo $queue->pub_count; ?>
			</div>
		</div>
		<div class="grid_1">
			<span class="icon <?php echo ($src->isPub_active() ? 'true' : 'false'); ?>"></span>
		</div>
		<div class="grid_1">
			<a href="<?php echo Config::Web('/Index/queueOrder', 'top', $queue->id); ?>" class="button">
				<span class="span icon top" style="color: white;" />
			</a>
			<a href="<?php echo Config::Web('/Index/queueOrder', 'up', $queue->id); ?>" class="button">
				<span class="span icon up" style="color: white;" />
			</a>
			<a href="<?php echo Config::Web('/Index/queueOrder', 'down', $queue->id); ?>" class="button">
				<span class="span icon down" style="color: white;" />
			</a>
		</div>
	</div>
	<span id='<?php echo "queued_" . $queue->id; ?>'></span>
</div>
<?php endforeach; ?>

</section>

<script type="text/javascript">
$(document).ready(function(){
	$('body').on('click', 'a.queued', function (e) {
		var safe_guid = "queued_"+ $(this).attr('data-queue_id');
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/Index/ajax_queueItems'); ?>",
			data: {
				queue_id: $(this).attr('data-queue_id')
			},
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = $('#' + safe_guid);
				ajaxDisplay.hide();
				ajaxDisplay.empty().append(msg);
				ajaxDisplay.fadeIn('slow');
			}
		});
		e.stopPropagation();
		return false;
	});
});
</script>
