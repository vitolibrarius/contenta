<?php use \html\ProgressBar as ProgressBar; $progress = new ProgressBar(); ?>
<style type="text/css">
.row.data {
	background-color: #e3e3e3;
	vertical-align: middle;
	border-bottom: 1px solid #FFFFFF;
}
</style>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index'); ?>">Wanted</a></li>
	</ul>
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index_series'); ?>">Wanted Series</a></li>
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
					<a href="#" class="wanted" data-story_arc_id="<?php echo $value->id; ?>"><?php echo $value->displayName(); ?></a>
				</h2>
			</div>
			<div class="grid_1">
				<span>
					<span class="icon <?php echo ($value->isPub_active() ? 'true' : 'false'); ?>"></span>
				</span>
			</div>
			<div class="grid_3">
				<span><?php $pub = $value->lastPublication();
					if ( $pub != false ) {
						echo $pub->seriesName() . " " . $pub->paddedIssueNum() . " - " . $pub->publishedMonthYear();
					}
				?></span>
			</div>
			<div class="grid_2">
				<?php echo $progress->render($value); ?>
			</div>
		</div>
		<span id='<?php echo "wanted_" . $value->id; ?>'></span>
		<?php endforeach; ?>
    </div>
</section>



<script type="text/javascript">
	$('body').on('click', 'a.wanted', function (e) {
		var safe_guid = "wanted_"+ $(this).attr('data-story_arc_id');
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/pubsWanted'); ?>",
			data: {
				story_arc_id: $(this).attr('data-story_arc_id')
			},
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = $('#' + safe_guid);
				ajaxDisplay.empty().append(msg);
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
				issue: $(this).attr('data-issue'),
				year: $(this).attr('data-year'),
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
				var divId = "ajaxDiv_"+pub_id;
				var ajaxDisplay = document.getElementById(divId);
				ajaxDisplay.innerHTML = msg;
			}
		});
		e.stopPropagation();
		return false;
	});

</script>
