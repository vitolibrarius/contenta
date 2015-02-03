<script>
	$(document).ready(function() {
		$("#<?php echo $this->input_id; ?>").select2( { width: 'resolve' } );
	});
</script>

<label for="<?php echo $this->input_id; ?>">
<?php echo $this->input_label; ?>
<?php if ($this->input_restriction != null) {
	echo '<span class="input_restriction">' . $this->input_restriction .'</span>';
}
?>
</label>
<!-- <?php echo $this->input_value; ?>  -->
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
