<div><!-- container -->
	<div><!-- top -->
		<div style="padding:15px; display:inline-block; top:0; width: auto; vertical-align:top;"><!-- right -->
			<form method="post" style="min-width: 380px;" accept-charset="utf-8"
				action="<?php echo Config::Web($this->saveAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php
					$legendValue = ucwords( $this->model->tableName()) . " Details";
					echo Localized::ModelLabel($this->model->tableName(),"FormLegend", $legendValue); ?>
				</legend>

				<?php if (isset($this->object) == false) : ?>
					<?php
						$attr = model\Endpoint::type_id;
						$attrName = $this->model->attributeName(null, null, $attr);

						$this->renderFormField( \Model::TO_ONE_TYPE, null, null, $this->model, $attr, null, true );
					?>
				<?php else : ?>
					<label>Error</label>
				<?php endif; ?>

				<div class="half">
					<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("NextButton"); ?>" />
				</div>

				<div class="half omega">
					<input type="reset" value="<?php echo Localized::GlobalLabel("ResetButton"); ?>"/>
				</div>
				</fieldset>
			</form>
		</div>
	</div>
</div><!-- container -->
