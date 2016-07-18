<script language="javascript" type="text/javascript">
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
				img: '<?php echo Config::Web("/AdminMedia/thumbnailForMedia/") ?>' +'/' + $(this).attr('data_key'),
				description: '<?php echo $this->label( "media", "DeleteDescription"); ?> <br /><em>' + $(this).attr('data_filename') + '</em>',
				confirm: '<?php echo $this->label( "media", "DeleteConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
				action: $(this).attr('data_action')
			});
			e.preventDefault();
		});
	});
</script>

<section>
	<div class="row">
		<div class="grid_4">

		<form method="post" accept-charset="utf-8"
			action="<?php echo Config::Web($this->saveAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
			name="editForm">

			<fieldset>
			<legend><?php echo Localized::ModelLabel($this->model->tableName(),"FormLegend"); ?></legend>

			<?php
				$realObj = (isset($this->object)) ? $this->object : null;
				$realType = null;
			?>

			<?php foreach ($this->model->attributesFor($realObj, $realType) as $attr => $form_type) : ?>

				<div class="">
				<?php $attrEditable = $this->model->attributeIsEditable($realObj, $realType, $attr);
					$attrName = $this->model->attributeName($realObj, $realType, $attr);
					$attValue = null;
					if ( isset($_POST, $_POST[$attrName]) ) {
						$attValue = $_POST[$attrName];
					}
					else if (isset($this->{$attr})) {
						$attValue = $this->{$attr};
					}

					$this->renderFormField( $form_type, $realObj, $realType, $this->model, $attr, $attValue, $attrEditable );
				?>
				</div>
			<?php endforeach; ?>
			<br>

			<div class="half">
				<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("SaveButton"); ?>" />
			</div>

			<div class="half omega">
				<input type="reset"value="<?php echo Localized::GlobalLabel("ResetButton"); ?>"/>
			</div>
			</fieldset>
		</form>

		</div>
	<?php if (isset($this->object, $this->additionalAction) && null != $this->object->externalEndpoint()) : ?>
		<div class="grid_4">

		<form method="post"
			action="<?php echo Config::Web($this->additionalAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
			name="editForm">

			<fieldset>
			<legend><?php echo Localized::ModelLabel($this->model->tableName(),"AdditionalLegend"); ?></legend>

				<?php $endpointType = $this->object->externalEndpoint()->endpointType(); ?>
				<label>Metadata Source</label>
				<div>
					<span class="input_restriction">
						<a href="<?php echo $this->object->xurl; ?>" target="<?php echo $endpointType->name; ?>">
							<?php echo $endpointType->name; ?>
							<img style="display:inline; float:right; max-width: 20px; max-height: 20px;"
								src="<?php echo $endpointType->favicon(); ?>">
						</a>
					</span>
				</div>

				<label><?php echo Localized::ModelLabel($this->model->tableName(), "xupdated"); ?></label>
				<input class="xupdated" type="text" name="xupdated" disabled
					value="<?php echo $this->object->formattedDate('xupdated'); ?>"
				/>

				<div class="half">
					<label><?php echo Localized::ModelLabel($this->model->tableName(), Model::IconName); ?></label>
					<img src="<?php echo Config::Web( "Image", "icon", $this->model->tableName(), $this->object->id); ?>" />
				</div>

				<div class="half omega">
					<label><?php echo Localized::ModelLabel($this->model->tableName(), Model::ThumbnailName); ?></label>
					<img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $this->object->id); ?>" />
				</div>

			<div>
				<input type="submit" name="refreshEndpoint" value="<?php echo Localized::GlobalLabel("RefreshFromEndpoint"); ?>" />
			</div>
			</fieldset>
		</form>

		</div>
	<?php endif; ?>

		<?php if (isset($this->object)) : ?>
			<div class="grid_3">
				<div class="badges">
				<?php $list = $this->object->characters();
					if ( is_array($list) && count($list) > 0 ): ?>
					<h6>Characters</h6>
					<ul class="badge characters">
					<?php foreach ($list as $character): ?>
						<li class="character">
							<?php if ( isset($this->editCharacterAction) ): ?>
								<a href="<?php echo Config::Web($this->editCharacterAction, $character->id); ?>">
							<?php endif; ?>
							<?php echo $character->name; ?>
							<?php if ( isset($this->editCharacterAction) ) :?></a><?php endif; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php $list = $this->object->story_arcs();
					if ( is_array($list) && count($list) > 0 ): ?>
					<h6>Story Arcs</h6>
					<ul class="badge story_arc">
					<?php foreach ($list as $story): ?>
						<li class="story_arc">
							<?php if ( isset($this->editStoryArcAction) ): ?>
								<a href="<?php echo Config::Web($this->editStoryArcAction, $story->id); ?>">
							<?php endif; ?>
							<?php echo $story->name; ?>
							<?php if ( isset($this->editStoryArcAction) ) :?></a><?php endif; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>

<?php if (isset($this->object) && null != $this->object->media()) : ?>
<section>
	<div class="row data">
		<div class="grid_12">
			<h3 class="group"><?php echo Localized::ModelLabel($this->model->tableName(), "media"); ?></h3>
		</div>
	</div>
	<div class="row">
		<?php $mediaList = $this->object->media();
			foreach( $mediaList as $media ) : ?>
			<div class="grid_3">
				<figure class="card">
					<div class="figure_top">
						<div class="figure_image">
							<img src="<?php echo Config::Web('/AdminMedia/thumbnailForMedia', $media->id) ?>" class="thumbnail">
						</div>
						<div class="figure_details">
							<div class="figure_detail_top">
							</div>
							<div class="figure_detail_middle">
								<p class="status">
									<?php echo $media->formattedSize() ?>
								</p>
							</div>
							<div class="figure_detail_bottom">
								<div class="rpc_tools">
									<a href="<?php echo Config::Web('/Api/mediaPayload/' . $media->id)?>">
										<img src="<?php echo Config::Web('/public/img/download.png' ) ?>">
									</a>
									<a href="<?php echo Config::Web('/DisplaySeries/mediaSlideshow/' . $media->id)?>" target="slideshow">
										<img src="<?php echo Config::Web('/public/img/slideshow.png' ) ?>">
									</a>
								</div>
							</div>
						</div>
					</div>

					<figcaption class="caption">
						<div style="text-align: center;">
							<a href="<?php echo Config::Web('/AdminMedia/reprocessMedia', $media->id); ?>" class="button" alt="Reprocess">
								<?php echo $this->label( "media", "ReprocessButtonText"); ?>
							</a>
							<a href="#" class="confirm button"
								data_action="<?php echo Config::Web('/AdminPublication/deleteMedia', $media->id); ?>"
								data_key="<?php echo $media->id; ?>"
								data_filename="<?php echo (isset($media->filename) ? $media->filename : $media->id); ?>"
								alt="Delete">
									<?php echo $this->label( "media", "DeleteButtonText"); ?>
							</a>
						</div>
					</figcaption>
				</figure>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>
