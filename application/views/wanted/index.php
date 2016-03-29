<style>
.row.data {
	background-color: #e2e2e2;
	vertical-align: middle;
	border-bottom: 1px solid #FFFFFF;
}
.mediaData table {
	width: 100%;
}
</style>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index_series'); ?>">Wanted Series</a></li>
	</ul>
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index_story_arc'); ?>">Wanted Story Arcs</a></li>
	</ul>
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/newznab'); ?>">Manual Search</a></li>
	</ul>
</div>

<section>
    <div class="wrapper">
		<?php for($offset = 0; $offset < 12; $offset++) : ?>
		<?php $date = new DateTime();
			$date->modify('-'.$offset.' months');
		?>
		<div class="row data">
			<div class="grid_6">
				<h2 style="display: inline">
					<a href="#" class="wanted" data-date_range="<?php echo $offset; ?>">
						<?php echo $date->format('F Y'); ?></a>
				</h2>
			</div>
			<div class="grid_1">
				<span></span>
			</div>
			<div class="grid_1">
				<span>
				</span>
			</div>
			<div class="grid_2">
				<span></span>
			</div>
			<div class="grid_2">
				<span style="float: right;">
				</span>
			</div>
		</div>
		<span id='<?php echo "ajaxDiv_" . $offset; ?>'></span>
		<?php endfor; ?>
    </div>
</section>



<script type="text/javascript">
$(document).ready(function(){
	$('body').on('click', 'a.wanted', function (e) {
		var safe_guid = "ajaxDiv_"+ $(this).attr('data-date_range');
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/pubsWanted'); ?>",
			data: {
				date_range: $(this).attr('data-date_range')
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

	$('body').on('click', 'a.nzb', function (e) {
		var ref_guid = "#" + $(this).attr('data-ref_guid');
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/downloadNewznab'); ?>",
			data: {
				endpoint_id: $(this).attr('data-endpoint_id'),
				name: $(this).attr('data-name'),
				guid: $(this).attr('data-guid'),
				nzburl: $(this).attr('data-url'),
				postedDate: $(this).attr('data-postedDate')
			},
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = $(ref_guid);
				ajaxDisplay.hide();
				ajaxDisplay.empty().append(msg);
				ajaxDisplay.fadeIn(100).show();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		});
		e.stopPropagation();
		return false;
	});

	$('body').on('click', 'a.srch', function (e) {
		var pub_id = $(this).attr('data-pub_id');
		$(this).fadeOut(100).hide();
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/newznabQuicksearch/');?>"+pub_id,
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = $("#ajaxDiv_"+pub_id);
				ajaxDisplay.hide();
				ajaxDisplay.empty().append(msg);
				ajaxDisplay.fadeIn(100).show();
			}
		});
		e.stopPropagation();
		return false;
	});

});
</script>
