	<section id="content">
		<div class="row">
			<div class="grid_4"></div>
			<div class="grid_4">

	<form method="post" name="login"
		action="<?php echo Config::Web( (isset($this->loginActionPath) ? $this->loginActionPath : '/Login/login') )?>">

		<fieldset>
		<legend><?php echo Localized::ModelLabel( $this->model->tableName(), "LoginLegend", "Login" ); ?></legend>

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
		<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("LoginButton"); ?>" />

		</fieldset>
	</form>
			</div>
			<div class="grid_4"></div>
		</div>
	</section>

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
