<script language="javascript" type="text/javascript">
	$('body').on('click', 'a.confirm', function (e) {
		var ref_guid = "#media_" + $(this).attr('data_key');
		var href = $(this).attr('data_action');
		modal.open({
			heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
			img: '<?php echo Config::Web("/AdminMedia/iconForMedia/") ?>' + '/' + $(this).attr('data_key'),
			description: '<?php echo $this->label( "index", "DeleteDescription"); ?> <br /><em>' + $(this).attr('data_filename') + '</em>',
			confirm: '<?php echo $this->label( "index", "DeleteConfirmation"); ?>',
			actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
			action: function() {
				var ajaxDisplay = $(ref_guid);
				var ssrc = "<?php echo Config::Web('/public/select2-spinner.gif'); ?>";
				var spin = $('<img class="rpc_spinner" src="' + ssrc + '" />' );
				ajaxDisplay.empty().append(spin);
				$.ajax({
					type: "GET",
					url: href,
					success: function(msg){
						var ajaxDisplay = $(ref_guid);
						ajaxDisplay.empty().append(msg);
					}
				});
			}
		});
		e.preventDefault();
	});
</script>

<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<input type="text" name="searchSeries" id="searchSeries"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "series_id" ); ?>"
				value="">
		</div>
		<div class="grid_3">
			<select name="searchCharacter" id="searchCharacter"
					class="text_input">
			</select>
		</div>
		<div class="grid_3">
			<select name="searchStoryArcs" id="searchStoryArcs"
					class="text_input">
			</select>
		</div>
		<div class="grid_1">
			<input type="number" name="searchIssue" id="searchIssue"
				min="0"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "issue_num" ); ?>"
				value="">
			</input>
		</div>
		<div class="grid_1">
			<input type="number" name="searchYear" id="searchYear"
				min="1950"
				max="<?php echo intval(date("Y") + 1); ?>"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "pub_date" ); ?>"
				value="">
			</input>
		</div>
		<div class="grid_1">
			<label class="checkbox" for="searchMedia" >
				<input type="checkbox"  name="searchMedia" id="searchMedia"
					class="text_input"
					value="1"
					checked>
				</input>
				<?php echo Localized::ModelSearch($this->model->tableName(), "multiple" ); ?>
			</label>
		</div>
	</div>
	</form>
</section>

<div id='ajaxDiv'></div>

<script type="text/javascript">
$(document).ready(function($) {
	search_timer = 0;

	$("#searchCharacter").select2({
		multiple: true,
		width: '100%',
		placeholder: "<?php echo Localized::ModelSearch($this->model->tableName(), 'characters' ); ?>",
		allowClear: true,
		ajax: {
			url: "<?php echo Config::Web('/Api/characters'); ?>",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data) {
				return {
					results: $.map(data, function(obj) {
						return { id: obj.id, text: obj.name };
					})
				};
			},
			cache: true
		}
	}).on("change", function(e) {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	$("#searchStoryArcs").select2({
		multiple: true,
		width: '100%',
		placeholder: "<?php echo Localized::ModelSearch($this->model->tableName(), 'story_arcs' ); ?>",
		allowClear: true,
		ajax: {
			url: "<?php echo Config::Web('/Api/story_arcs'); ?>",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data) {
				return {
					results: $.map(data, function(obj) {
						return { id: obj.id, text: obj.name };
					})
				};
			},
			cache: true
		}
	}).on("change", function(e) {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	$(".text_input").on('keyup', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	function refresh() {
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminMedia/searchMedia'); ?>",
			data: {
				series_name: $('#searchSeries').val(),
				character_id: $('#searchCharacter').val(),
				story_arc_id: $('#searchStoryArcs').val(),
				issue: $('input#searchIssue').val(),
				media: $('input#searchMedia').is(':checked'),
				year:  $('input#searchYear').val()
			},
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = document.getElementById('ajaxDiv');
				ajaxDisplay.innerHTML = msg;
			}
		});
	};
	refresh();
});
</script>

<script type="text/javascript">
$(document).ready(function($) {
	$('#searchField').trigger('keyup');
});
</script>
