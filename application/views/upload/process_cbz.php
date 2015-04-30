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

<script type="text/javascript">
<!--
images = [<?php foreach ($this->fileWrapper->wrapperContents() as $index => $item) {
	echo "'" . Config::Web('/AdminUploadRepair/thumbnail', $this->key, $index) . "', ";
}?>];
fullsizedURL="<?php echo Config::Web('/AdminUploadRepair/fullsized', $this->key); ?>/";
//-->
</script>

<div><!-- container -->
	<div><!-- top -->
		<div style="padding:15px; display:inline-block;"><!-- left -->
			<div id="slideshow">
				<img src="<?php echo Config::Web('/AdminUploadRepair/thumbnail', $this->key, 0); ?>" id="page">
				<span class="button" id="leftArrow"></span>
				<span class="button" id="rightArrow"></span>
			</div>

			<div>
				<a id="fullsizelink" class="btn" target="FullSizedImage" href="#">Fullsized Image</a>
			</div>
		</div>
		<div style="display:inline-block; top:0; width: auto; vertical-align:top;"><!-- right -->
			<form action="<?php echo Config::Web('/AdminUploadRepair/cbz_updateMetadata/', $this->key) ?>" method="post">
				<fieldset>
					<legend><h3 class="path"><?php echo $this->source['name']; ?></h3></legend>

					<div class="half">
						<label>File Size</label>
						<input type="text" disabled
							value="<?php echo (isset($this->source['size']) ? formatSizeUnits( $this->source['size']) : 'Unknown'); ?>" />
					</div>

					<div class="half omega">
						<label>Pages</label>
						<input type="text" disabled
							value="<?php echo $this->fileWrapper->wrapperContentCount(); ?>" />
					</div>
					<br>

					<label for="series">Series Name</label>
					<input type="text" name="series" placeholder="Justice League United"
						value="<?php echo (isset($this->search['name']) ? $this->search['name'] : ''); ?>" />

					<div class="third">
						<label for="volume">Volume</label>
						<input type="text" name="volume" placeholder=""
							value="<?php echo (isset($this->search['volume']) ? $this->search['volume'] : ''); ?>" />
					</div>

					<div class="third">
						<label for="issue">Issue</label>
						<input type="text" name="issue" placeholder="01"
							value="<?php echo (isset($this->search['issue']) ? $this->search['issue'] : ''); ?>" />
					</div>

					<div class="third omega">
						<label for="year">Published Year</label>
						<input type="text" name="year" placeholder="2014"
							value="<?php echo (isset($this->search['year']) ? $this->search['year'] : '');  ?>" />
					</div>
					<br />

					<div class="half">
						<label></label>
						<input type="submit" name="search" value="Search"/>
					</div>

					<div class="half omega">
						<label></label>
						<input type="submit" name="reset" value="Reset"/>
					</div>

				</fieldset>
			</form>
			<div id="ajaxDiv_<?php echo $this->key; ?>"></div>

		</div>
	</div>

	<div style=""> <!-- bottom -->
		<div id='comicVineTable'></div>
	</div>
</div><!-- container -->

<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$('#fullsizelink').click(function(e) {
		var i = jQuery.inArray($('#page').attr('src'), images);
		var href = fullsizedURL + (i < 0 ? 0 : i);
	    $('#fullsizelink').attr('href', href);
	});

	$.ajax({
		type: "GET",
		url: "<?php echo Config::Web('/AdminUploadRepair/cbz_initialComicVine/', $this->key); ?>",
		dataType: "text",
		success: function(msg, textStatus, jqXHR) {
			var ajaxDisplay = document.getElementById('comicVineTable');
			if ( jqXHR.status == 204 ) {
				refreshComicVine();
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
});

function refreshComicVine() {
	$.ajax({
		type: "GET",
		url: "<?php echo Config::Web('/AdminUploadRepair/cbz_refreshComicVine/', $this->key); ?>",
		dataType: "text",
		success: function(msg){
			var ajaxDisplay = document.getElementById('comicVineTable');
			ajaxDisplay.innerHTML = msg;
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert('Unexpected error.');
		}
	});
}

</script>
