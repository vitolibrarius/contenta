<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "type_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "job_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "processor" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "desc" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "pid" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "elapsedTime" ); ?></th>
		</tr>
	<?php if (is_array($this->objects) && count($this->objects) > 0): ?>
		<?php foreach($this->objects as $key => $value): ?>
			   <tr>
					<td><?php echo ($value->jobType() != false ? $value->jobType()->name : ""); ?></td>
					<td><?php echo ($value->job() != false ? $value->job()->displayName() : "" ); ?></td>
					<td><?php echo $value->processor; ?></td>
					<td><?php echo $value->desc; ?></td>
					<td><?php echo $value->pid; ?></td>
					<td><?php echo $value->elapsedFormatted(); ?></td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan=6><?php echo 'No processes running'; ?></td>
		</tr>
	<?php endif ?>

	</table>
</div>
