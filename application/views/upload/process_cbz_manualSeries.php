<style type="text/css">
	#slideshow #leftArrow {
		background:url('<?php echo Config::Web("/public/img/left.png"); ?>') no-repeat;
		background-position:left center;
	}
	#slideshow #leftArrow:hover {
		background:url('<?php echo Config::Web("/public/img/left2.png"); ?>') no-repeat;
		background-position:left center;
	}
	#slideshow #rightArrow {
		background:url('<?php echo Config::Web("/public/img/right.png"); ?>') no-repeat;
		background-position:right center;
	}
	#slideshow #rightArrow:hover {
		background:url('<?php echo Config::Web("/public/img/right2.png"); ?>') no-repeat;
		background-position:right center;
	}

</style>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminUploadRepair/editUnprocessed', $this->key); ?>">Automated Search</a></li>
		<li><a href="#" class="confirm"
				data_action="<?php echo Config::Web('/AdminUploadRepair/deleteUnprocessed', $this->key); ?>"
				data_key="<?php echo $this->key; ?>"
				data_filename="<?php echo $this->source['name']; ; ?>"
				alt="Delete">
				<span class="icon trash" ></span> Delete
			</a>
		</li>
	</ul>
</div>

<script type="text/javascript">
<!--
images = [<?php foreach ($this->fileWrapper->imageContents() as $index => $item) {
	echo "'" . Config::Web('/AdminUploadRepair/thumbnail', $this->key, $index) . "', ";
}?>];
fullsizedURL="<?php echo Config::Web('/AdminUploadRepair/fullsized', $this->key); ?>/";
//-->
</script>

<section>
	<div class="row">
		<div class="grid_2">
			<div id="slideshow">
				<img src="<?php echo Config::Web('/AdminUploadRepair/thumbnail', $this->key, 0); ?>" id="page">
				<span class="button" id="leftArrow"></span>
				<span class="button" id="rightArrow"></span>
			</div>

			<div>
				<a id="fullsizelink" class="button" target="FullSizedImage" href="#">Fullsized Image</a>
			</div>
		</div>
		<div class="grid_6">
			<form action="#" method="post">
				<fieldset>
					<legend><h3 class="path"><?php echo $this->source['name']; ?></h3></legend>

					<div class="">
						<label>File Size</label>
						<input type="text" disabled
							value="<?php echo (isset($this->source['size']) ? formatSizeUnits( $this->source['size']) : 'Unknown'); ?>" />
					</div>

					<div class="">
						<label>Pages</label>
						<input type="text" disabled
							value="<?php echo $this->fileWrapper->wrapperContentCount(); ?>" />
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</section>


<section>
	<div class="row">
		<div class="grid_12">
			<ul class="tabs">
				<li><a href="#tab1">Search Existing Series</a></li>
				<li><a href="#tab2">Create New Series</a></li>
			</ul>
		</div>
	</div>
</section>

<div class="tab-content" id="tab1">
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
					value="<?php echo (isset($this->search['name']) ? $this->search['name'] : ''); ?>">
			</div>
			<div class="grid_1">
				<input type="number" name="searchYear" id="searchYear"
					class="text_input"
					min="1950"
					max="<?php echo intval(date("Y") + 1); ?>"
					placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "year" ); ?>"
					value="<?php echo (isset($this->search['year']) ? $this->search['year'] : '');  ?>">
			</div>
		</div>
		</form>
	</section>

	<div id='ajaxDiv'></div>
</div>

<div class="tab-content" id="tab2">
	<?php $this->renderEditForm("series"); ?>
</div> <!-- tab 2 -->



<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$('#fullsizelink').click(function(e) {
		var i = jQuery.inArray($('#page').attr('src'), images);
		var href = fullsizedURL + (i < 0 ? 0 : i);
	    $('#fullsizelink').attr('href', href);
	});

	var list = $('a.confirm');
	$('a.confirm').click(function(e){
		modal.open({
			heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
			img: '<?php echo Config::Web("/AdminUploadRepair/firstThumbnail/") ?>' + '/' + $(this).attr('data_key'),
			description: '<?php echo $this->label( "index", "DeleteDescription"); ?> <br /><em>' + $(this).attr('data_filename') + '</em>',
			confirm: '<?php echo $this->label( "index", "DeleteConfirmation"); ?>',
			actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
			action: $(this).attr('data_action')
		});
		e.preventDefault();
	});

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

	$(".text_input").on('keyup change', function () {
		delay( refresh(), 250 );
	});

	function refresh() {
		$.ajax({
			type: "GET",
			url: "<?php echo Config::Web('/AdminUploadRepair/editUnprocessedManually_seriesList', $this->key); ?>",
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
