<section>
	<div class="row">
		<div class="grid_6">

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
			<div class="grid_3">

			<form method="post"
				action="<?php echo Config::Web($this->additionalAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"AdditionalLegend"); ?></legend>

					<div class="">
						<label><?php echo Localized::ModelLabel($this->model->tableName(), "xupdated"); ?></label>
						<input class="xupdated" type="text" name="xupdated" disabled
							value="<?php echo $this->object->formattedDate('xupdated'); ?>"
						/>
					</div>

					<div class="">
						<label><?php echo Localized::ModelLabel($this->model->tableName(), "mediaPath"); ?></label>
						<input class="mediaPath" type="text" name="mediaPath" disabled
							value="<?php echo $this->object->mediaPath(); ?>"
						/>
					</div>

					<div class="">
						<label><?php echo Localized::ModelLabel($this->model->tableName(), Model::IconName); ?></label>
						<img src="<?php echo Config::Web( "Image", "icon", $this->model->tableName(), $this->object->id); ?>" />
					</div>

					<div class="">
						<label><?php echo Localized::ModelLabel($this->model->tableName(), Model::ThumbnailName); ?></label>
						<img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $this->object->id); ?>" />
					</div>
					<br>

				<div>
					<input type="submit" name="refreshEndpoint" value="<?php echo Localized::GlobalLabel("RefreshFromEndpoint"); ?>" />
				</div>
				</fieldset>
			</form>

			</div>
		<?php endif; ?>

	</div>
</section>

<?php if (isset($this->object)) : ?>
<section>
	<div class="row">
		<?php if (empty($this->object->publications())): ?>
			<div style="background:hsl(326,50%,75%)">
				There are no publications
			</div>
		<?php else: ?>
			<?php
				$card = new html\Card();
				$card->setDisplayDescriptionKey( "shortDescription" );
				$card->setDetailKeys( array(
					model\Publication::issue_num => "issue_num",
					model\Publication::pub_date => "publishedMonthYear",
					)
				);

				foreach($this->object->publications() as $key => $value) {
					if ( isset($this->editPublicationAction) ) {
						$card->setEditPath( $this->editPublicationAction . '/' . $value->id );
					}
					if ( isset($this->deletePublicationAction) ) {
						$card->setDeletePath( $this->deletePublicationAction . '/' . $value->id );
					}
					echo '<div class="grid_3">' . PHP_EOL;
					echo $card->render($value);
					echo '</div>' . PHP_EOL;
				}
			?>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>
