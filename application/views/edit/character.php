<div><!-- container -->
	<div><!-- top -->
		<div style="padding:15px; display:inline-block; top:0; width: auto; vertical-align:top;"><!-- right -->
			<form method="post" style="min-width: 380px;" accept-charset="utf-8"
				action="<?php echo Config::Web($this->saveAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"FormLegend"); ?></legend>

				<?php
					$realObj = (isset($this->object)) ? $this->object : null;
					$realType = null;
				?>

				<?php foreach ($this->model->attributesFor($realObj, $realType) as $attr => $form_type) {
						$attrEditable = $this->model->attributeIsEditable($realObj, $realType, $attr);
						$attrName = $this->model->attributeName($realObj, $realType, $attr);
						$attValue = null;
						if ( isset($_POST, $_POST[$attrName]) ) {
							$attValue = $_POST[$attrName];
						}
						else if ( $form_type == Model::FLAG_TYPE && count($_POST) > 0) {
							$attValue = 0;
						}
						else if (isset($this->{$attr})) {
							$attValue = $this->{$attr};
						}

						$this->renderFormField( $form_type, $realObj, $realType, $this->model, $attr, $attValue, $attrEditable );
					}
				?>
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
		<div style="padding:15px; display:inline-block; top:0; width: auto; vertical-align:top;"><!-- right -->
			<form method="post" style="min-width: 380px;"
				action="<?php echo Config::Web($this->additionalAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"AdditionalLegend"); ?></legend>

					<label><?php echo Localized::ModelLabel($this->model->tableName(), "xupdated"); ?></label>
					<input class="xupdated" type="text" name="xupdated" disabled
						value="<?php echo $this->object->formattedDate('xupdated'); ?>"
					/>

					<label><?php echo Localized::ModelLabel($this->model->tableName(), "small_icon_name"); ?></label>
					<img src="<?php echo Config::Web( "Image", "icon", $this->model->tableName(), $this->object->id); ?>" />

					<label><?php echo Localized::ModelLabel($this->model->tableName(), "large_icon_name"); ?></label>
					<img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $this->object->id); ?>" />

				<div>
					<input type="submit" name="refreshEndpoint" value="<?php echo Localized::GlobalLabel("RefreshFromEndpoint"); ?>" />
				</div>
				</fieldset>
			</form>
		</div>
		<?php endif; ?>
	</div>
</div><!-- container -->
