<div><!-- container -->
	<div><!-- top -->
		<div style="padding:15px; display:inline-block; top:0; width: auto; vertical-align:top;"><!-- right -->
			<form method="post" style="min-width: 380px;"
				action="<?php echo Config::Web($this->saveAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php
					$legendValue = ucwords( $this->model->tableName()) . " Details";
					echo $this->modelLabel($this->model->tableName(),"FormLegend", $legendValue); ?>
				</legend>

				<?php if (isset($this->object)) : ?>
					<label><?php echo $this->modelLabel($this->model->tableName(), "created"); ?></label>
					<input class="date_input" type="text" name="created" disabled
						value="<?php if (isset($this->object)) { echo $this->object->formattedDate("created"); } ?>"
					/>
				<?php endif; ?>

				<?php
					$realObj = (isset($this->object)) ? $this->object : null;
					$realType = ( isset($this->object) ? $this->object->type() :
						isset($this->endpoint_type) ? $this->endpoint_type : null );
				?>
					<label>Type</label>
					<div>
						<span class="input_restriction" style="width:90%; float:left;">
							ComicVine provides a detailed information source about comics and graphic novels that allow Contenta to automatically load and categorize uploaded media content.  Please see the ComicVine home page for information on abtaining an API key.
						</span>
						<a href="<?php echo $realType->site_url; ?>" target="<?php echo $realType->name; ?>">
							<img style="display:inline; float:right;" src="<?php echo $realType->favicon(); ?>">
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
					<input type="submit" name="edit_submit" value="<?php echo $this->globalLabel("SaveButton", "Save"); ?>" />
				</div>

				<div class="half omega">
					<input type="reset"value="<?php echo $this->globalLabel("ResetButton", "Reset"); ?>"/>
				</div>
				</fieldset>
			</form>
		</div>
	</div>
</div><!-- container -->
