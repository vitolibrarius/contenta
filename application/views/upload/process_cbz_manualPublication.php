<?php use html\Element as H ?>
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
				<li><a href="#tab1">Existing Publications</a></li>
				<li><a href="#tab2">Create New Publication</a></li>
			</ul>
		</div>
	</div>
</section>

<div class="tab-content" id="tab1">
	<section>
		<div class="row">
		<?php if (empty($this->listArray)): ?>
			<div style="background:hsl(326,50%,75%)">
				There are no matching records
			</div>
		<?php else: ?>
		<?php
			$card = new html\Card();
			$card->setDisplayDescriptionKey( "shortDescription" );
			$card->setDetailKeys( array(
				\model\media\Publication::issue_num => "issue_num",
				\model\media\Publication::pub_date => "publishedMonthYear"
				)
			);
			if ( is_null($this->listArray) || count($this->listArray) == 0) {
				echo "No records";
			}

			foreach($this->listArray as $key => $value) {
				echo '<div class="grid_3">' . PHP_EOL;
				echo $card->render($value, function() use($value) {
						$all_media = $value->media();
						if ( is_array($all_media) ) {
							foreach ($all_media as $idx => $media) {
								$c[] = H::p( $media->formattedSize() );
							}
						}

						$c[] = H::a(
							array(
								"class" => "button",
								"href" => Config::Web($this->saveAction, $value->id)),
							"Select"
						);
						return (isset($c) ? $c : null);
					}
				);
				echo '</div>' . PHP_EOL;
			}
		?>
		<?php endif; ?>
		</div>
	</section>
</div>

<div class="tab-content" id="tab2">
	<?php $this->renderEditForm("publication"); ?>
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
			img: '<?php echo Config::Web("/AdminUploadRepair/firstThumbnail/") ?>' + $(this).attr('data_key'),
			description: '<?php echo $this->label( "index", "DeleteDescription"); ?> <br /><em>' + $(this).attr('data_filename') + '</em>',
			confirm: '<?php echo $this->label( "index", "DeleteConfirmation"); ?>',
			actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
			action: $(this).attr('data_action')
		});
		e.preventDefault();
	});
});

</script>
