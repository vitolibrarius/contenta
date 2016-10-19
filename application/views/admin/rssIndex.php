<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<input type="text" name="searchName" id="searchName"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "clean_name" ); ?>"
				value="">
		</div>
		<div class="grid_1">
			<input type="text" name="searchIssue" id="searchIssue"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "clean_issue" ); ?>"
				value="">
			</input>
		</div>
		<div class="grid_1">
			<input type="number" name="searchYear" id="searchYear"
				min="1950"
				max="<?php echo intval(date("Y") + 1); ?>"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "clean_year" ); ?>"
				value="">
			</input>
		</div>
		<div class="grid_1">
			<input type="number" name="searchAge" id="searchAge"
				min="0"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "pub_date" ); ?>"
				value="">
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
	$(".text_input").on('keyup change', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	function refresh() {
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminPullList/searchRss'); ?>",
			data: {
				name: $('input#searchName').val(),
				issue: $('input#searchIssue').val(),
				year:  $('input#searchYear').val(),
				age:  $('input#searchAge').val()
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
