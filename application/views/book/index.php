<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<input type="text" name="searchName" id="searchName"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "name" ); ?>"
				value="">
		</div>
		<div class="grid_3">
			<input type="text" name="searchAuthor" id="searchAuthor"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "author" ); ?>"
				value="">
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
		var ajaxDisplay = document.getElementById('ajaxDiv');
		ajaxDisplay.innerHTML = "<em>searching</em>";
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/DisplayBook/searchBooks'); ?>",
			data: {
				name: $('input#searchName').val(),
				author:  $('input#searchAuthor').val()
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
