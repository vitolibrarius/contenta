<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/netconfig/index'); ?>"><span class="">Endpoints</span></a></li>
	</ul>
</div>

	<section id="content">
		<div class="row">
			<div class="grid_4">

			<form method="post" accept-charset="utf-8"
				action="<?php echo Config::Web($this->saveAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"FormLegend"); ?></legend>

				<?php
					$realObj = (isset($this->object)) ? $this->object : null;
					$realType = ( isset($this->object) ? $this->object->type() :
						(isset($this->endpoint_type) ? $this->endpoint_type : null));
				?>
					<label>Type</label>
					<div>
						<span class="input_restriction" style="width:90%; float:left;"><?php echo $realType->comments; ?></span>
						<a href="<?php echo $realType->site_url; ?>" target="<?php echo $realType->name; ?>">
							<img style="display:inline; float:right; max-width: 20px; max-height: 20px;"
								src="<?php echo $realType->favicon(); ?>">
						</a>
					</div>
					<input class="type" type="text" name="displayType" disabled
						value="<?php echo $realType->displayName(); ?>"
					/>
					<input class="type" type="hidden" name="endpoint-type_id"
						value="<?php echo $realType->id; ?>"
					/>

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
		</div>
	</section>
