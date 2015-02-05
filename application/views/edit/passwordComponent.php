<label for="<?php echo $this->input_id; ?>">
<?php echo $this->input_label; ?>
<?php if ($this->input_restriction != null) {
	echo '<span class="input_restriction">' . $this->input_restriction .'</span>';
}
?>
</label>
<input id="<?php echo $this->input_id; ?>"
	class="text_input"
	type="password"
	name="<?php echo $this->input_name; ?>"
	<?php if ($this->input_pattern != null) {
		echo 'pattern="' . $this->input_pattern . '"';
	} ?>
	<?php if ($this->input_placeholder != null) {
		echo 'placeholder="' . $this->input_placeholder . '"';
	} ?>
	value="<?php echo $this->input_value; ?>"
/>
