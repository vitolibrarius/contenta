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
		<li><a href="<?php echo Config::Web('/AdminUploadRepair/editUnprocessedManually', $this->key); ?>">Manual Processing</a></li>
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
		<div class="grid_8">
			<div id='epubTable'></div>
		</div>
	</div>
</section>



<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$('#fullsizelink').click(function(e) {
		var i = jQuery.inArray($('#page').attr('src'), images);
		var href = fullsizedURL + (i < 0 ? 0 : i);
	    $('#fullsizelink').attr('href', href);
	});

	$.ajax({
		type: "GET",
		url: "<?php echo Config::Web('/AdminUploadRepair/epub_initial/', $this->key); ?>",
		dataType: "text",
		success: function(msg, textStatus, jqXHR) {
			var ajaxDisplay = document.getElementById('epubTable');
			if ( jqXHR.status == 204 ) {
				refreshEpub();
			}
			else if (jqXHR.status == 203 ) {
				ajaxDisplay.innerHTML = "Incomplete data, try reset of filename components";
			}
			else {
				ajaxDisplay.innerHTML = msg;
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert('Unexpected error.');
		}
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
});

function refreshEpub() {
	$.ajax({
		type: "GET",
		url: "<?php echo Config::Web('/AdminUploadRepair/epub_refreshEpub/', $this->key); ?>",
		dataType: "text",
		success: function(msg){
			var ajaxDisplay = document.getElementById('epubTable');
			ajaxDisplay.innerHTML = msg;
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert('Unexpected error.');
		}
	});
}

</script>
