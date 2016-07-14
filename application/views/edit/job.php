<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminJobs/index'); ?>"><span class="">Jobs</span></a></li>
	</ul>
</div>

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
					$realType = (isset($this->job_type) ? $this->job_type : null);
					if ( is_null($realObj) == false && is_null($realObj->jobType()) == false ) {
						$realType = $realObj->jobType();
					}
				?>
					<label><?php echo Localized::ModelLabel($this->model->tableName(),"type_code"); ?></label>
					<input class="type" type="text" name="displayType" disabled
						value="<?php echo (is_null($realType) ? '' : $realType->displayName()); ?>"
					/>
					<input class="type" type="hidden" name="job-type_code"
						value="<?php echo $realType->code; ?>"
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
