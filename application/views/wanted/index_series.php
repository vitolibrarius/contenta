<style type="text/css">
.row.data {
	background-color: #e2e2e2;
	vertical-align: middle;
	border-bottom: 1px solid #FFFFFF;
}
</style>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index'); ?>">Wanted</a></li>
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
		<?php foreach($this->listArray as $key => $value) : ?>
		<div class="row data">
			<div class="grid_6">
				<h2 style="display: inline">
					<a href="#" class="wanted" data-series_id="<?php echo $value->id; ?>"><?php echo $value->displayName(); ?></a>
				</h2>
			</div>
			<div class="grid_1">
				<span><?php echo $value->start_year; ?></span>
			</div>
			<div class="grid_1">
				<span>
					<span class="icon <?php echo ($value->isPub_active() ? 'true' : 'false'); ?>"></span>
				</span>
			</div>
			<div class="grid_2">
				<span><?php $pub = $value->lastPublication();
					if ( $pub != false ) {
						echo "Issue " . $pub->paddedIssueNum() . " - " . $pub->publishedMonthYear();
					}
				?></span>
			</div>
			<div class="grid_2">
				<span style="float: right;">
					Issues <?php echo (isset($value->pub_available)?$value->pub_available:0); ?>
					/ <?php echo $value->pub_count; ?>
				</span>
			</div>
		</div>
		<span id='<?php echo "wanted_" . $value->id; ?>'></span>
		<?php endforeach; ?>
    </div>
</section>



<script type="text/javascript">
$(document).ready(function(){
	$('body').on('click', 'a.wanted', function (e) {
		var safe_guid = "wanted_"+ $(this).attr('data-series_id');
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/pubsWanted'); ?>",
			data: {
				series_id: $(this).attr('data-series_id')
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
			url: "<?php echo Config::Web('/AdminWanted/newznabQuicksearch/');?>"+'/'+pub_id,
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
