<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "job_type_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "job_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "processor" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "pid" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "elapsedTime" ); ?></th>
		</tr>
	<?php if (is_array($this->objects) && count($this->objects) > 0): ?>
		<?php foreach($this->objects as $key => $value): ?>
			   <tr>
					<td><?php echo (is_null($value->jobType()) ? "" : $value->jobType()->name); ?></td>
					<td><?php echo (is_null($value->job()) ? "" : $value->job()->displayName()); ?></td>
					<td><?php echo $value->processor; ?></td>
					<td><?php echo $value->pid; ?></td>
					<td><?php echo $value->elapsedFormatted(); ?></td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan=5><?php echo 'No processes running'; ?></td>
		</tr>
	<?php endif ?>

	</table>
</div>
