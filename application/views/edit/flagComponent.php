<div>
<?php if ($this->input_restriction != null) {
	echo '<span class="input_restriction">' . $this->input_restriction .'</span>';
}
?>
<label class="checkbox" for="<?php echo $this->input_id; ?>">
<input id="<?php echo $this->input_id; ?>"
	class="flag_input"
	type="checkbox"
	name="<?php echo $this->input_name; ?>"
	<?php if ($this->input_placeholder != null) {
		echo ' value="' . $this->input_placeholder . '"';
	} ?>
	<?php if (isset($this->input_disabled) && boolValue($this->input_disabled, false)) {
		echo 'disabled="disabled" ';
	} ?>
	<?php if ($this->input_value === 'on' || (intval($this->input_value) > Model::TERTIARY_FALSE )) {
		echo ' checked';
	} ?>
/>
<?php echo $this->input_label; ?>
</label>
</div>
