<script language="javascript" type="text/javascript">
	// Wait until the DOM has loaded before querying the document
	$('body').on('click', 'a.confirm', function (e) {
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
</script>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminPublishers/index'); ?>">Publishers</a></li>
		<li><a href="<?php echo Config::Web('/AdminSeries/index'); ?>">Series</a></li>
		<li><a href="<?php echo Config::Web('/AdminStoryArcs/index'); ?>">Story Arcs</a></li>
		<li><a href="<?php echo Config::Web('/AdminPublication/index'); ?>">Publications</a></li>
		<li><a href="<?php echo Config::Web( '/AdminCharacters/comicVineSearch' ); ?>"><span class="">ComicVine Import</span></a></li>
	</ul>
</div>

<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_4">
			<select name="publisher_id" id="publisher_id" class="text_input">
				<option></option>
			</select>
		</div>
		<div class="grid_4">
			<input type="text" name="name" id="name"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "name" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchName') : ''); ?>">
		</div>
		<div class="grid_2">
			<input type="number" name="popularity" id="popularity"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "popularity" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('popularity') : ''); ?>">
		</div>
	</div>
	</form>
</section>


<div id='ajaxDiv'></div>

<script type="text/javascript">
$(document).ready(function($) {
	search_timer = 0;

	var $select = $("#publisher_id");
	$select.select2({
		allowClear: true,
		width: "element",
		placeholder: "<?php echo Localized::ModelSearch($this->model->tableName(), 'publisher_id' ); ?>",
		ajax: {
			url: "<?php echo Config::Web('/Api/publishers'); ?>",
			dataType: 'json',
			delay: 250,
			cache: true,
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
	});

<?php if (isset($this->params) && $this->params->valueForKey('publisher_id') != null) : ?>
	var $initialValue = "<?php echo $this->params->valueForKey('publisher_id'); ?>";
	var $option = $('<option selected>Loading...</option>').val($initialValue);
	$select.append($option).trigger('change');
	$.ajax({
		type: 'GET',
		url: "<?php echo Config::Web('/Api/publishers'); ?>" +"?id=" + $initialValue,
		dataType: 'json'
	}).then(function (data) {
		if (typeof data !== 'undefined' && data.length > 0) {
			$option.text(data[0].name).val(data[0].id);
		}
		$option.removeData();
		$select.trigger('change');
	});
<?php endif; ?>

	$select.on("change", function(e) {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	$(".text_input").on('keyup', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 400);
	});

	function refresh() {
		var page_url = "<?php echo Config::Web('/AdminCharacters/searchCharacters'); ?>";
		var resultsId = "ajaxDiv";
		var inputValues = $("form#searchForm").serializeObject();

		refreshAjax( page_url, undefined, inputValues, resultsId );
	};
	refresh();
});
</script>
