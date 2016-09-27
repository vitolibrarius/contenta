<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/netconfig/index'); ?>"><span class="">Endpoints</span></a></li>
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
					$realType = (isset($this->endpoint_type) ? $this->endpoint_type : null);
					if ( is_null($realObj) == false && is_null($realObj->endpointType()) == false ) {
						$realType = $realObj->endpointType();
					}
				?>
					<label>Type</label>
					<div>
						<span class="input_restriction">
							<a href="<?php echo $realType->site_url; ?>" target="<?php echo $realType->name; ?>">
								<img style="display:inline; float:right; max-width: 20px; max-height: 20px;"
									src="<?php echo $realType->favicon(); ?>">
							</a>
							<?php echo $realType->comments; ?>
						</span>
					</div>
					<input class="type" type="text" name="displayType" disabled
						value="<?php echo $realType->displayName(); ?>"
					/>
					<input class="type" type="hidden" name="endpoint-type_code"
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
				<br>
				</fieldset>
			</form>

			</div>

			<?php if ( isset($this->object)): ?>
			<div class="grid_4">
				<div>
					<a href="#" class="test button" style="white-space:nowrap;"
						data-href="<?php echo Config::Web($this->testAction, $this->object->id); ?>"
						>
						Test Connection
					</a>
					<pre id="ajaxDiv"></pre>
				</div>
				<div>
					<a href="<?php echo Config::Web($this->clearErrorsAction, $this->object->id); ?>" class="button" style="white-space:nowrap;">
						Reset Error Count
					</a>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</section>

<script type="text/javascript">
	$('body').on('click', 'a.test', function (e) {
		$.ajax({
			type: "GET",
			url: $(this).attr('data-href'),
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = $('#ajaxDiv');
				ajaxDisplay.empty().append(msg);
			}
		});
		e.stopPropagation();
		return false;
	});
</script>
