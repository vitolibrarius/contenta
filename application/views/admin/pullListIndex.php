<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<select name="searchPullList" id="searchPullList"
					class="text_input">
				<option></option>
			</select>
		</div>
		<div class="grid_2">
			<input type="text" name="searchName" id="searchName"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "name" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchName') : ''); ?>">
		</div>
	</div>
	</form>
</section>

<div id='ajaxDiv'></div>

<script type="text/javascript">
$(document).ready(function($) {
	search_timer = 0;
	$("#searchPullList").select2({
		placeholder: "<?php echo Localized::ModelSearch($this->model->tableName(), 'pull_list_id' ); ?>",
		allowClear: true,
		ajax: {
			url: "<?php echo Config::Web('/Api/pull_lists'); ?>",
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
		search_timer = setTimeout(refresh, 1000);
	});

	$(".text_input").on('keyup change', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 1000);
	});

	function refresh() {
		var page_url = "<?php echo Config::Web('/AdminPullList/searchPullLists'); ?>";
		var resultsId = "ajaxDiv";
		var inputValues = $("form#searchForm").serializeObject();
		console.log( JSON.stringify(inputValues) );

		refreshAjax( page_url, undefined, inputValues, resultsId );
	};
});
</script>
