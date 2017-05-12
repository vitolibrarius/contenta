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
</div><?php
	$adminPage = "Admin" . $this->model->modelName() . "/edit" . $this->model->modelName();
	$keys = array( "series/name", "name", "lastXupdated", "formattedDate_search_date", "publishedMonthYear", "issue_num"  );
	$activeKp = "series/isPub_active";
?>
<hr>
<h1>Total :<?php echo $this->total; ?></h1>
<hr>

<?php	include 'PublicationWantedCard.php'; if (count($this->results) > 1): ?>
<?php foreach($this->results as $idx => $publication): ?>
<?php if ( $idx % 3 == 0) : ?>
	<?php $open = true; ?>
	<div class="row">
<?php endif; // modulo ?>
		<div class="grid_4">
		<?php
			$card = new PublicationWantedCard($publication);
			$card->render();
		?>
		</div>

<?php if ( ($idx +1) % 3 == 0 ) : ?>
	<?php $open = false; ?>
	</div>
<?php endif; // modulo ?>

<?php endforeach; ?>
<?php if (isset($open) && $open == true) : ?>
	</div>
<?php endif; // open ?>
<?php else: ?>
	<em>No matching records</em>
<?php endif; ?>

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
