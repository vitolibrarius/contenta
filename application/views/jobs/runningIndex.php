<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminJobs/index'); ?>"><span class="">Job Schedules</span></a></li>
		<li><a href="<?php echo Config::Web('/AdminJobs/edit'); ?>"><span class="">Add New Job</span></a></li>
	</ul>
</div>

<!--
			Job_Running::id, Job_Running::job_id, Job_Running::job_type_id,
			Job_Running::processor, Job_Running::guid,
			Job_Running::created, Job_Running::pid

-->
<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "job_type_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "job_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "processor" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "pid" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "guid" ); ?></th>
		</tr>
	<?php if (is_array($this->objects) && count($this->objects) > 0): ?>
		<?php foreach($this->objects as $key => $value): ?>
			   <tr>
					<td><?php echo $value->jobType()->name; ?></td>
					<td><?php echo $value->job()->displayName(); ?></td>
					<td><?php echo $value->processor; ?></td>
					<td><?php echo $value->pid; ?></td>
					<td><?php echo $value->guid; ?></td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan=5><?php echo 'No processes running'; ?></td>
		</tr>
	<?php endif ?>

	</table>
</div>
