<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index'); ?>">Wanted</a></li>
	</ul>
</div>

<section>
    <div class="wrapper">
	<form id='searchForm' name='searchForm'>
		<div class="row">
			<div class="grid_3">
				<select name="searchEndpoint" id="searchEndpoint"
						class="text_input">
					<?php foreach ($this->endpoints as $key => $endpoint) {
						echo '<option value="' . $endpoint->pkValue() . '"';
						echo '>' . $endpoint->displayName() . '</option>';
					}
					?>
				</select>
			</div>
			<div class="grid_4">
				<input type="text" name="searchName" id="searchName"
					class="text_input"
					placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "newznab" ); ?>"
					value="<?php echo (isset($this->searchString)?$this->searchString:''); ?>">
			</div>
		</div>
	</form>
	</div>
</section>

<div id='ajaxDiv'></div>


<script type="text/javascript">
$(document).ready(function($) {
	$("#searchEndpoint").select2({
		placeholder: "<?php echo Localized::ModelSearch($this->model->tableName(), 'endpoint_id' ); ?>",
		allowClear: true
	}).on("change", function(e) {
		delay( refreshResults(), 250 );
	});

	$('.text_input').keypress(function (e) {
		if (e.which == 13) {
			refreshResults();
			e.preventDefault();
   			e.stopPropagation();

			return false;    //<---- Add this line
		}
	});

	function refreshResults() {
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/searchNewznab'); ?>",
			data: {
				endpoint_id: $('#searchEndpoint').val(),
				search: $('#searchName').val()
			},
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = document.getElementById('ajaxDiv');
				ajaxDisplay.innerHTML = msg;
			}
		});
	};
});

$(document).ajaxComplete(function(){
	$('body').on('click', 'a.nzb', function (e) {
		var safe_guid = $(this).attr('data-safe_guid');
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminWanted/downloadNewznab'); ?>",
			data: {
				endpoint_id: $(this).attr('data-endpoint_id'),
				name: $(this).attr('data-name'),
				guid: $(this).attr('data-guid'),
				nzburl: $(this).attr('data-url'),
				postedDate: $(this).attr('data-postedDate')
			},
			dataType: "text",
			success: function(msg){
				var divId = "ajaxDiv_"+safe_guid;
				var ajaxDisplay = document.getElementById(divId);
				ajaxDisplay.innerHTML = msg;
			}
		});
		e.stopPropagation();
		return false;
	});
});
</script>
