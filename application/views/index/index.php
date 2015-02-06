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
<br>
<br>
<?php endif; ?>

<?php if (Session::get('user_logged_in') == true):?>

<div class="span_container">
	<div class="span_container_row header">
		<span class="">Feature</span>
		<span class="">Expected Version</span>
		<span class="">System Version</span>
	</div>
	<div class="span_container_row">
		<span class="">PHP</span>
		<span class=""></span>
		<span class="">
			<?php
			if (PHP_VERSION_ID < 57207) {
				echo '<b style="color:red;">';
			}
			echo phpversion();
			if (PHP_VERSION_ID < 57207) {
				echo '</b>';
			}
			?>
		</span>
	</div>

	<?php foreach (get_loaded_extensions() as $key => $extension): ?>
		<div class="span_container_row">
			<span class=""><?php echo $extension; ?></span>
			<span class=""></span>
			<span class="">
				<?php echo phpversion($extension) ; ?>
			</span>
		</div>
	<?php endforeach; ?>

</div>
<?php endif; ?>

