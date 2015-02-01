<label for="<?php echo $this->input_id; ?>">
<?php echo $this->input_label; ?>
<?php if ($this->input_restriction != null) {
	echo '<span>' . $this->input_restriction .'</span>';
}
?>
</label>
<textarea id="<?php echo $this->input_id; ?>" 
	class="textarea_input" 
	type="text" 
	name="<?php echo $this->input_name; ?>"
	<?php if ($this->input_pattern != null) {
		echo 'pattern="' . $this->input_pattern . '"';
	} ?>
	<?php if ($this->input_placeholder != null) {
		echo 'placeholder="' . $this->input_placeholder . '"';
	} ?>
><?php echo $this->input_value; ?></textarea>
