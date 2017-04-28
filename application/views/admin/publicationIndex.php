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
		<li><a href="<?php echo Config::Web('/AdminCharacters/index'); ?>">Characters</a></li>
		<li><a href="<?php echo Config::Web('/AdminStoryArcs/index'); ?>">Story Arcs</a></li>
		<li><a href="<?php echo Config::Web( '/AdminPublication/comicVineSearch' ); ?>"><span class="">ComicVine Import</span></a></li>
	</ul>
</div>

<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<input type="text" name="searchSeries" id="searchSeries"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "series_id" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchSeries') : ''); ?>">
		</div>
		<div class="grid_3">
			<select name="searchCharacter" id="searchCharacter"
					class="text_input">
			</select>
		</div>
		<div class="grid_3">
			<select name="searchStoryArcs" id="searchStoryArcs"
					class="text_input">
			</select>
		</div>
		<div class="grid_1">
			<input type="text" name="searchIssue" id="searchIssue"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "issue_num" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchIssue') : ''); ?>">
			</input>
		</div>
		<div class="grid_1">
			<input type="number" name="searchYear" id="searchYear"
				min="1950"
				max="<?php echo intval(date("Y") + 1); ?>"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "pub_date" ); ?>"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('searchYear') : ''); ?>">
			</input>
		</div>
		<div class="grid_1">
			<label class="checkbox" for="searchMedia" >
				<input type="checkbox"  name="searchMedia" id="searchMedia"
					class="text_input"
					value="1"
					<?php echo (isset($this->params) && $this->params->valueForKey('searchWanted') ? "checked" : ""); ?>">
				</input>
				Has Media
			</label>
		</div>
	</div>
	</form>
</section>

<div id='ajaxDiv'></div>

<script type="text/javascript">
$(document).ready(function($) {
	search_timer = 0;

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
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 1000);
	});

	$("#searchStoryArcs").select2({
		multiple: true,
		width: '100%',
		placeholder: "<?php echo Localized::ModelSearch($this->model->tableName(), 'story_arcs' ); ?>",
		allowClear: true,
		ajax: {
			url: "<?php echo Config::Web('/Api/story_arcs'); ?>",
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

	$(".text_input").on('keyup', function () {
		if (search_timer) {
			clearTimeout(search_timer);
		}
		search_timer = setTimeout(refresh, 1000);
	});

	function refresh() {
		var page_url = "<?php echo Config::Web('/AdminPublication/searchPublication'); ?>";
		var resultsId = "ajaxDiv";
		var inputValues = $("form#searchForm").serializeObject();
		console.log( JSON.stringify(inputValues) );

		refreshAjax( page_url, undefined, inputValues, resultsId );
	}
	refresh();
});
</script>

<script type="text/javascript">
$(document).ready(function($) {
	$('#searchField').trigger('keyup');
});
</script>
