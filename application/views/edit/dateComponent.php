<!-- script>
	$(document).ready(function() {
		$("#<?php echo $this->input_id; ?>").datepicker();
	});
</script -->

<label for="<?php echo $this->input_id; ?>">
<?php echo $this->input_label; ?>
	<?php if ($this->input_restriction != null) : ?>
		<span class="input_restriction"><?php echo $this->input_restriction; ?></span>
	<?php endif; ?>
	<?php if ($this->input_validation != null) : ?>
		<span class="input_validation"><?php echo UTF8_STAR . ' ' . $this->input_validation; ?></span>
	<?php endif; ?>
</label>
<input id="<?php echo $this->input_id; ?>"
	class="date_input"
	type="text"
	name="<?php echo $this->input_name; ?>"
	<?php if ($this->input_pattern != null) {
		echo 'pattern="' . $this->input_pattern . '"';
	} ?>
	<?php if (isset($this->input_disabled) && boolValue($this->input_disabled, false)) {
		echo 'disabled="disabled" ';
	} ?>
	<?php if ($this->input_placeholder != null) {
		echo 'placeholder="' . $this->input_placeholder . '"';
	} ?>
	value="<?php if (isset($this->input_value)) { echo date('m/d/Y', $this->input_value); } ?>"
/>
