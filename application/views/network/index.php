<div class="mediaData">
	<table>
		<tr>
			<th>Type</th>
			<th>Name</th>
			<th>Base URL</th>
			<th>API Key</th>
			<th>Username</th>
			<th>Enabled</th>
			
		</tr>
	<?php if (is_array($this->endpoints)): ?>
		<?php foreach($this->endpoints as $key => $value): ?>
			   <tr>
					<td><?php echo $value->type()->name; ?></td>
					<td><?php echo htmlentities($value->name); ?></td>
					<td><?php echo $value->base_url; ?></td>
					<td><?php echo $value->api_key; ?></td>
					<td><?php echo $value->username; ?></td>
					<td><?php echo $value->enabled; ?></td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<?php echo 'No endpoints yet. Create some !'; ?>
	<?php endif ?>

	</table>
</div>
