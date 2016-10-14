<form id='searchForm' name='searchForm'>
	<div>
		<div style="display: inline-block; min-width: 300px;">
		<select name="searchPublisher" id="searchPublisher"
				class="text_input">
			<option></option>
		</select>
		</div>
		<div style="display: inline-block;">
		<input type="text" name="searchSeries" id="searchSeries"
			class="text_input"
			placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "series_id" ); ?>"
			value="">
		</div>
		<div style="display: inline-block;">
		<input type="text" name="searchName" id="searchName"
			class="text_input"
			placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "name" ); ?>"
			value="">
		</div>
	</div>
</form>

<div id='ajaxDiv'></div>

<script type="text/javascript">
$(document).ready(function($) {
	search_timer = 0;
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
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	$(".text_input").on('keyup change', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	function refresh() {
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/DisplayStories/searchStoryArcs'); ?>",
			data: {
				series_name: $('#searchSeries').val(),
				publisher_id: $('#searchPublisher').val(),
				name: $('input#searchName').val()
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
