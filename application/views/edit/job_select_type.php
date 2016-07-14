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
				<legend><?php
					echo Localized::ModelLabel($this->model->tableName(),"FormLegend"); ?>
				</legend>

				<?php if (isset($this->object) == false) : ?>
					<?php
						$attr = \model\jobs\Job::type_code;
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
	</section>
