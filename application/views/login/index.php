<form style="display:block; width:200px; margin-left:auto; margin-right:auto;" name='login'
	action="<?php echo Config::Web( isset_default($this->loginActionPath, '/login/login') )?>" method="post">
	<fieldset>
	<legend>Login</legend>

	<label for='username'>Username (or email)</label>
	<input type="text" id='username' placeholder="JohnDoe" name="user_name" required />

	<label for='password'>Password</label>
	<input type="password" id='password' placeholder="" name="user_password" required />

	<div>
		<label class="checkbox remember-me-label" for="remember-me-label">Keep me logged in (for 2 weeks)</label>
		<input type="checkbox" id="remember-me-label" name="user_rememberme" class="remember-me-checkbox" />
	</div>


	<label>&nbsp; </label>
	<input type="submit" class="login-submit-button" />
	</fieldset>
</form>

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
