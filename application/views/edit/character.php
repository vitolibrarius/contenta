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

				<?php foreach ($this->model->attributesFor($realObj, $realType) as $attr => $form_type) {
						$attrEditable = $this->model->attributeIsEditable($realObj, $realType, $attr);
						$attrName = $this->model->attributeName($realObj, $realType, $attr);
						$attValue = null;
						if ( isset($_POST, $_POST[$attrName]) ) {
							$attValue = $_POST[$attrName];
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
			<div class="grid_3">

			<form method="post"
				action="<?php echo Config::Web($this->additionalAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">
				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"AliasesLegend"); ?></legend>

					<label><?php echo Localized::ModelLabel($this->model->tableName(), "aliases"); ?></label>
					<?php foreach ($this->object->aliases() as $alias ) : ?>
					<input class="xupdated" type="text" name="xupdated" disabled
						value="<?php echo $alias->name; ?>"
					/>
					<?php endforeach; ?>

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

					<?php $endpointType = $this->object->externalEndpoint()->type(); ?>
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

		</div>
	</section>
