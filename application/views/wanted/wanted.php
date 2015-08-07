<div class="mediaData">
	<table>
		<tr>
			<th></th>
			<th>Type</th>
			<th>Name</th>
			<th>Size</th>
			<th></th>
		</tr>
	<?php foreach ($this->listArray as $key => $publication): ?>
		<tr>
			<td><img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $publication->id); ?>"
				class="thumbnail recordType" />
			</td>
			<td><?php echo $publication->seriesName(); ?></td>
			<td><?php echo $publication->name; ?></td>
			<td><?php echo $publication->issue_num; ?></td>
			<td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
