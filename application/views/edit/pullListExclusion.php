<form method="post" accept-charset="utf-8"
	id="exclForm"
	name="saveExclusion">

	<fieldset>
	<legend>Pull_List_Excl</legend>

	<?php foreach ($this->exclusion_model->attributesFor($this->pullListExclusion, null) as $attr => $form_type) {
			$attrEditable = $this->exclusion_model->attributeIsEditable($this->pullListExclusion, null, $attr);
			$attrName = $this->exclusion_model->attributeName($this->pullListExclusion, null, $attr);
			$attValue = null;
			if ( isset($_POST, $_POST[$attrName]) ) {
				$attValue = $_POST[$attrName];
			}
			else if (isset($this->{$attr})) {
				$attValue = $this->{$attr};
			}

			$this->renderFormField( $form_type, $this->pullListExclusion, null, $this->exclusion_model, $attr, $attValue, $attrEditable );
		}
	?>
	<div class="half">
		<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("SaveButton"); ?>" />
	</div>
	<div class="half omega">
		<input type="submit" name="edit_delete" value="<?php echo Localized::GlobalLabel("DeleteButton"); ?>" />
	</div>

	<div class="half">
		<input type="submit" name="edit_test" value="<?php echo Localized::GlobalLabel("TestButton"); ?>" />
	</div>
	<div class="half omega">
		<input type="reset"value="<?php echo Localized::GlobalLabel("ResetButton"); ?>"/>
	</div>
	</fieldset>
</form>

<script type="text/javascript">
$(document).ready(function($) {
	var request;

	$("#exclForm").submit(function(event){

		event.preventDefault();

		// Abort any pending request
		if (request) {
			request.abort();
		}
		// setup some local variables
		var $form = $(this);

		// Let's select and cache all the fields
		var $inputs = $form.find("input, select, button, textarea");

		// Serialize the data in the form
		var serializedData = $form.serialize();

		// Let's disable the inputs for the duration of the Ajax request.
		// Note: we disable elements AFTER the form data has been serialized.
		// Disabled form elements will not be serialized.
		$inputs.prop("disabled", true);

		console.log( JSON.stringify(serializedData) );

		// Fire off the request to /form.php
		var action="<?php echo Config::Web('AdminPullList/saveExclusion'); ?>/<?php echo $this->pullListExclusion->pkValue(); ?>"
		request = $.ajax({
			url: action,
			type: "post",
			data: serializedData
		});

		// Callback handler that will be called on success
		request.done(function (response, textStatus, jqXHR){
			// Log a message to the console
			console.log("Hooray, it worked!");
		});

		// Callback handler that will be called on failure
		request.fail(function (jqXHR, textStatus, errorThrown){
			// Log the error to the console
			console.error(
				"The following error occurred: "+
				textStatus, errorThrown
			);
		});

		// Callback handler that will be called regardless
		// if the request failed or succeeded
		request.always(function () {
			// Reenable the inputs
			$inputs.prop("disabled", false);
		});

});

});
</script>
