<label for="<?php echo $this->input_id; ?>">
<?php echo $this->input_label; ?>
	<?php if ($this->input_restriction != null) : ?>
		<span class="input_restriction"><?php echo $this->input_restriction; ?></span>
	<?php endif; ?>
	<?php if ($this->input_validation != null) : ?>
		<span class="input_validation"><?php echo UTF8_STAR . ' ' . $this->input_validation; ?></span>
	<?php endif; ?>
</label>
<?php if (isset($this->input_disabled) && boolValue($this->input_disabled, false)) : ?>
	<input id="<?php echo $this->input_id; ?>"
		class="text_input"
		type="text"
		name="<?php echo $this->input_name; ?>"
		disabled="disabled"
		value="<?php echo ( isset($this->input_object) && $this->input_object != false ? $this->input_object->displayName() : ''); ?>"
	/>
<?php else : ?>
	<script>
		$(document).ready(function() {
			$("#<?php echo $this->input_id; ?>").select2( { width: '100%' } );
		});
	</script>
	<select id="<?php echo $this->input_id; ?>" class="select_input"  name="<?php echo $this->input_name; ?>" >
		<option value="-1"> - Select - </option>
	<?php foreach ($this->input_options as $key => $option) {
		echo '<option value="' . $option->pkValue() . '"';
		if ( isset($this->input_value) && $this->input_value == $option->pkValue()) {
			echo " selected";
		}
		echo '>' . $option->displayName() . '</option>';
	}
	?>
</select>
<?php endif; ?>
