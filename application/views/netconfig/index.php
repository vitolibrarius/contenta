<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "type_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "base_url" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "api_key" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "username" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "enabled" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "compressed" ); ?></th>
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
					<td><?php echo $value->compressed; ?></td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<?php echo 'No endpoints yet. Create some !'; ?>
	<?php endif ?>

	</table>
</div>
