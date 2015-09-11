<div class="span_container">
<?php if (is_array($this->results) && count($this->results) > 0): ?>
	<div class="span_container_row header">
		<span class="nobreak"></span>
		<span class="break">Name</span>
		<span class="break">Description</span>
		<span class="break"></span>
	</div>
	<?php foreach ($this->results as $key => $record): ?>
		<div class="span_container_row">
			<span class="break">
				<?php if (isset($record['image'], $record['image']['tiny_url']) ){
					echo '<img src="' . $record['image']['tiny_url'] . '" />';
				} ?></span>
			</span>
			<span class="nobreak"><?php echo $record['name']; ?></span>
			<span class="break"><?php
				if (isset($record['deck']) ){
					echo strip_tags($record['deck']);
				} ?></span>
			<span class="nobreak">
				<?php if ($this->model->objectForExternal($record['id'], model\Endpoint_Type::ComicVine) == false) : ?>
				<a class="button" href="<?php echo Config::Web( $this->importAction, $record['id'], $record['name'] ); ?>">Import</a>
				<?php endif; ?>
			</span>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="span_container_row">
		<span class="log_date"><?php echo date('M d, Y  H:i', time()); ?></span>
		<span class="log_level">info</span>
		<span class="log_msg"><em>No publishers found</em></span>
	</div>
<?php endif; ?>
</div>
