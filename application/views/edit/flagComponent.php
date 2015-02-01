<label for="<?php echo $this->input_id; ?>">
<?php echo $this->input_label; ?>
<?php if ($this->input_restriction != null) {
	echo '<span style="display: block; font-size: 14px; color: #999;">' . $this->input_restriction .'</span>';
}
?>
</label>
<input id="<?php echo $this->input_id; ?>" 
	class="flag_input" 
	type="checkbox"
	name="<?php echo $this->input_name; ?>"
	<?php if ($this->input_placeholder != null) {
		echo ' value="' . $this->input_placeholder . '"';
	} ?>
	<?php if (intval($this->input_value) > Model::TERTIARY_FALSE ) {
		echo ' checked"';
	} ?>
/>
