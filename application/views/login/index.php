<div style="padding:15px; display:inline-block; top:0; width: auto; vertical-align:top;"><!-- right -->
	<form method="post" style="min-width: 380px;" name="login"
		action="<?php echo Config::Web( (isset($this->loginActionPath) ? $this->loginActionPath : '/Login/login') )?>">

		<fieldset>
		<legend><?php echo $this->modelLabel( $this->model->tableName(), "LoginLegend", "Login" ); ?></legend>

		<?php
			$attrName = "user_name";
			$attValue = null;
			if ( isset($_POST, $_POST[$attrName]) ) {
				$attValue = $_POST[$attrName];
			}

			$this->renderFormField( Model::TEXT_TYPE, null, null, $this->model, $attrName, $attValue);
		?>

		<?php
			$attrName = "user_password";
			$attValue = null;
			if ( isset($_POST, $_POST[$attrName]) ) {
				$attValue = $_POST[$attrName];
			}

			$this->renderFormField( Model::PASSWORD_TYPE, null, null, $this->model, $attrName, $attValue);
		?>

		<?php
			$attrName = "user_rememberme";
			$attValue = null;
			if ( isset($_POST, $_POST[$attrName]) ) {
				$attValue = $_POST[$attrName];
			}

			$this->renderFormField( Model::FLAG_TYPE, null, null, $this->model, $attrName, $attValue);
		?>

		<label>&nbsp; </label>
		<input type="submit" name="edit_submit" value="<?php echo $this->globalLabel("LoginButton", "Login"); ?>" />

		</fieldset>
	</form>
</div>

<?php if (isset($this->marquee)): ?>
<div id="marquee">
	<div class="photobanner">
		<?php foreach( $this->marquee as $idx => $src) {
			echo "<img ";
			if ($idx == 0) { echo "class='first'"; }
			echo "src='" . $src . "'/>";
		}
		?>
	</div>
</div>
<?php endif; ?>
