<script language="javascript" type="text/javascript">
	// Wait until the DOM has loaded before querying the document
	$(document).ajaxComplete(function(){
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
				img: '<?php echo Config::Web("/public/img/Logo_sm.png"); ?>',
				description: '<?php echo $this->label( "index", "DeleteDescription"); ?>',
				confirm: '<?php echo $this->label( "index", "DeleteConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
				action: $(this).attr('action')
			});
			e.preventDefault();
		});
	});
</script>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminPublishers/index'); ?>">Publishers</a></li>
		<li><a href="<?php echo Config::Web('/AdminSeries/index'); ?>">Series</a></li>
		<li><a href="<?php echo Config::Web('/AdminCharacters/index'); ?>">Characters</a></li>
		<li><a href="<?php echo Config::Web('/AdminPublication/index'); ?>">Publications</a></li>
		<li><a href="<?php echo Config::Web( '/AdminStoryArcs/comicVineSearch' ); ?>"><span class="">ComicVine Import</span></a></li>
	</ul>
</div>

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
		<div style="display: inline-block; min-width: 300px;">
		<select name="searchCharacter" id="searchCharacter"
				class="text_input">
		</select>
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
		delay( refresh(), 250 );
	});

	$(".text_input").on('keyup change', function () {
		delay( refresh(), 250 );
	});

	function refresh() {
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminStoryArcs/searchStoryArcs'); ?>",
			data: {
				series_name: $('#searchSeries').val(),
				character_id: $('#searchCharacter').val(),
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
