<script>
	$(document).ready(function() {
		$("#users-account_type").select2( { width: 'resolve' } );
	});
</script>

	<section>
		<div class="row">
			<div class="grid_5">

			<form method="post" accept-charset="utf-8"
				action="<?php echo Config::Web($this->saveAction); ?>/<?php echo (isset($this->object, $this->object->id)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"FormLegend"); ?></legend>

				<?php if (isset($this->object)) : ?>
					<label><?php echo Localized::ModelLabel($this->model->tableName(), "created"); ?></label>
					<input class="date_input" type="text" name="created" disabled
						value="<?php if (isset($this->object)) { echo $this->object->formattedDate("created"); } ?>"
					/>
				<?php endif; ?>

				<label for="users-account_type">
					Type <span style="display: block; font-size: 14px; color: #999;"></span>
				</label>
				<select id="users-account_type" class="select_input"  name="users-account_type" >
					<?php foreach ($this->userTypes as $key => $type) {
						echo '<option value="' . $key . '"';
						if ( isset($this->object) && $this->object->account_type === $key) {
							echo " selected";
						}
						echo '>' . $type . '</option>';
					} ?>
				</select>

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

						$this->renderFormField(
							$form_type,
							$realObj,
							$realType,
							$this->model,
							$attr,
							$attValue,
							$attrEditable,
							(isset($this->validationErrors, $this->validationErrors[$attr]) ? $this->validationErrors[$attr] : null)
						);
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

			<pre><?php echo (isset($this->validationErrors) ? var_export($this->validationErrors, true) : ""); ?></pre>
			</div>
		<?php if (isset($this->object)) : ?>
			<div class="grid_3">

			<form method="post"
				action="<?php echo Config::Web($this->additionalAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"AdditionalLegend"); ?></legend>

					<label><?php echo Localized::ModelLabel($this->model->tableName(), "api_hash"); ?></label>
					<input class="api" type="text" name="api_hash" disabled
						value="<?php if (isset($this->object)) { echo $this->object->api_hash; } ?>"
					/>

				<div>
					<input type="submit" name="generateAPI" value="<?php echo Localized::GlobalLabel("APIButton"); ?>" />
				</div>
				</fieldset>
			</form>

		</div>
		<?php endif; ?>

		</div>
	</section>
