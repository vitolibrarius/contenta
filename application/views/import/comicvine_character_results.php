<div class="span_container">
<?php if (is_array($this->results) && count($this->results) > 0): ?>
	<div class="span_container_row header">
		<span class="nobreak">Publisher</span>
		<span class="break">Name</span>
		<span class="break">Aliases</span>
		<span class="break">Description</span>
		<span class="break"></span>
	</div>
	<?php foreach ($this->results as $key => $record): ?>
		<div class="span_container_row">
			<span class="nobreak">
				<?php if (isset($this->pub_model, $record['publisher'], $record['publisher']['id']) ) {
					$publisher = $this->pub_model->objectForExternal($record['publisher']['id'], $this->endpoint->type()->code);
					if ( $publisher != false ) {
						if ( $publisher->hasIcons() ) {
							echo '<img src="' . Config::Web( "Image", "icon", $this->pub_model->tableName(), $publisher->id) . '" />';
						}
						echo htmlentities($publisher->name);
					}
					else {
						echo htmlentities($record['publisher']['name']);
					}
				} ?>
			</span>
			<span class="nobreak">
				<?php if (isset($record['image'], $record['image']['tiny_url']) ){
					echo '<img src="' . $record['image']['tiny_url'] . '" />';
				} ?>
				<?php echo $record['name']; ?>
			</span>
			<span class="nobreak"><?php
				if (isset($record['aliases']) ){
					$aliases = preg_split('/\n|\r/', $record['aliases'], -1, PREG_SPLIT_NO_EMPTY);
					echo implode("<br />", $aliases);
				} ?></span>
			<span class="break"><?php
				if (isset($record['deck']) ){
					echo strip_tags($record['deck']);
				} ?></span>
			<span class="nobreak">
				<?php if ($this->model->objectForExternal($record['id'], model\Endpoint_Type::ComicVine) == false) : ?>
				<a class="button" href="<?php echo Config::Web( $this->importAction, $record['id'], $record['name']  ); ?>">Import</a>
				<?php endif; ?>
			</span>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="span_container_row">
		<span class="log_date"><?php echo date('M d, Y  H:i', time()); ?></span>
		<span class="log_level">info</span>
		<span class="log_msg"><em>No characters found</em></span>
	</div>
<?php endif; ?>
</div>
