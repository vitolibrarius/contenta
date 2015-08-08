<form id='searchForm' name='searchForm'>
	<div>
		<div style="display: inline-block;">
		<input type="text" name="searchSeries" id="searchSeries"
			class="text_input"
			placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "series_id" ); ?>"
			value="">
		</div>
		<div style="display: inline-block; min-width: 300px;">
		<select name="searchStoryArcs" id="searchStoryArcs"
				class="text_input">
		</select>
		</div>
		<div style="display: inline-block;">
		<input type="number" name="searchIssue" id="searchIssue"
			min="0"
			class="text_input"
			placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "issue_num" ); ?>"
			value="">
		</input>
		</div>
		<div style="display: inline-block;">
		<input type="number" name="searchYear" id="searchYear"
			min="1950"
			max="<?php echo intval(date("Y") + 1); ?>"
			class="text_input"
			placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "pub_date" ); ?>"
			value="">
		</input>
		</div>
	</div>
</form>

<div id='ajaxDiv'></div>


<script type="text/javascript">
$(document).ready(function($) {
	$("#searchPublisher").select2({
		placeholder: "<?php echo Localized::ModelSearch($this->model->tableName(), 'publisher_id' ); ?>",
		allowClear: true,
		ajax: {
			url: "<?php echo Config::Web('/Api/publishers'); ?>",
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
		delay( refresh(), 250 );
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
					r: 'wanted',
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
		delay( refresh(), 250 );
	});

	$(".text_input").on('keyup change', function () {
		delay( refresh(), 250 );
	});

	function refresh() {
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/searchWanted'); ?>",
			data: {
				series_name: $('#searchSeries').val(),
				story_arc_id: $('#searchStoryArcs').val(),
				publisher_id: $('#searchPublisher').val(),
				issue: $('input#searchIssue').val(),
				year: $('input#searchYear').val()
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
