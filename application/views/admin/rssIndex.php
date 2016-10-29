<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<input type="text" name="searchName" id="searchName"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "clean_name" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchName') : ''); ?>">
		</div>
		<div class="grid_1">
			<input type="text" name="searchIssue" id="searchIssue"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "clean_issue" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchIssue') : ''); ?>">
			</input>
		</div>
		<div class="grid_1">
			<input type="number" name="searchYear" id="searchYear"
				min="1950"
				max="<?php echo intval(date("Y") + 1); ?>"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "clean_year" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchYear') : ''); ?>">
			</input>
		</div>
		<div class="grid_1">
			<input type="number" name="searchAge" id="searchAge"
				min="0"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "pub_date" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchAge') : ''); ?>">
			</input>
		</div>
		<div class="grid_1">
			<input type="number" name="searchSize" id="searchSize"
				min="0"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "enclosure_length" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchSize') : ''); ?>">
			</input>
		</div>
	</div>
	</form>
</section>

<div id='ajaxDiv'></div>

<script type="text/javascript">
var NZBDownload_url="<?php echo Config::Web('/AdminWanted/downloadNewznab'); ?>";

$(document).ready(function($) {
	search_timer = 0;
	$(".text_input").on('keyup', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	function refresh() {
		var page_url = "<?php echo Config::Web('/AdminPullList/searchRss'); ?>";
		var resultsId = "ajaxDiv";
		var inputValues = $("form#searchForm").serializeObject();
		console.log( JSON.stringify(inputValues) );

		refreshAjax( page_url, undefined, inputValues, resultsId );
	};
	refresh();
});
</script>
