<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<select name="searchPublisher" id="searchPublisher"
					class="text_input">
				<option></option>
			</select>
		</div>
		<div class="grid_3">
			<input type="text" name="searchName" id="searchName"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "name" ); ?>"
				value="">
		</div>
		<div class="grid_1">
			<input type="number" name="searchYear" id="searchYear"
				class="text_input"
				min="1950"
				max="<?php echo intval(date("Y") + 1); ?>"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "year" ); ?>"
				value="">
		</div>
	</div>
	</form>
</section>

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

	function refresh(change_count) {
		var ajaxDisplay = document.getElementById('ajaxDiv');
		ajaxDisplay.innerHTML = "<em>searching</em>";
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/DisplaySeries/searchSeries'); ?>",
			data: {
				publisher_id: $('#searchPublisher').val(),
				name: $('input#searchName').val(),
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
