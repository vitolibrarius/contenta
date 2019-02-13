<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<input type="text" name="searchName" id="searchName"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "name" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchName') : ''); ?>">
		</div>
		<div class="grid_3">
			<input type="text" name="searchAuthor" id="searchAuthor"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "author" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchAuthor') : ''); ?>">
		</div>
	</div>
	</form>
</section>

<div id='ajaxDiv'></div>


<script type="text/javascript">
$(document).ready(function($) {
	search_timer = 0;

	$(".text_input").on('keyup', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 1000);
	});

	function refresh(change_count) {
		var page_url = "<?php echo Config::Web('/DisplayBook/searchBooks'); ?>";
		var resultsId = "ajaxDiv";
		var inputValues = $("form#searchForm").serializeObject();
		console.log( JSON.stringify(inputValues) );

		refreshAjax( page_url, undefined, inputValues, resultsId );
	};
	refresh();
});
</script>
