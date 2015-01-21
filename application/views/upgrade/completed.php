<?php if (isset($this->logs)) : ?>
	<?php foreach( $this->logs as $filename => $data ): ?>
		<h2><?php echo $filename; ?></h2>
		<div class="change_log">
			<pre><?php echo $data; ?></pre>
		</div>
	<?php endforeach; ?>
<?php else : ?>
<?php endif; ?>
