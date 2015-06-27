<script>
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		var list = $('a.confirm');
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
				img: '<?php echo Config::Web("/public/img/Logo_sm.png"); ?>',
				description: '<?php echo $this->label( "index", "DeleteDescription"); ?>',
				confirm: '<?php echo $this->label( "index", "DeleteConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
				action: $(this).attr('action')
			});
			e.preventDefault();
		});
	});
</script>
<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminJobs/edit'); ?>"><span class="">Add New Job</span></a></li>
	</ul>
</div>

<!-- 	const id =			'id';
	const type_id =		'type_id';
	const endpoint_id =	'endpoint_id';
	const minute =		'minute';
	const hour =		'hour';
	const dayOfWeek =	'dayOfWeek';
	const one_shot =	'one_shot';
	const created =		'created';
	const next =		'next';
	const last_run =	'last_run';
	const parameter =	'parameter';
	const enabled =		'enabled';
-->
<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "type_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "endpoint_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "minute" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "hour" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "dayOfWeek" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "lastDate" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "nextDate" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "parameter" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "one_shot" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "enabled" ); ?></th>
			<th colspan=2></th>
		</tr>
	<?php if (is_array($this->objects)): ?>
		<?php foreach($this->objects as $key => $value): ?>
			   <tr>
					<td><?php echo $value->jobType()->name; ?></td>
					<td><?php echo $value->endpoint()->name; ?></td>
					<td><?php echo $value->minute; ?></td>
					<td><?php echo $value->hour; ?></td>
					<td><?php echo $value->dayOfWeek; ?></td>
					<td><?php echo $value->lastDate(); ?></td>
					<td><?php echo $value->nextDate(); ?></td>
					<td><?php echo var_export($value->jsonParameters(), true); ?></td>
					<td><span class="icon <?php echo ($value->one_shot ? 'true' : 'false') ?>"></span></td>
					<td><span class="icon <?php echo ($value->enabled ? 'true' : 'false') ?>"></span></td>


					<td><a href="<?php echo Config::Web('/AdminJobs/edit/'. $value->id); ?>"><span class="icon edit" /></a></td>
					<td><a class="confirm" action="<?php echo Config::Web('/AdminJobs/delete/') . $value->id; ?>" href="#">
						<span class="icon recycle"></span></a>
					</td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<?php echo 'No jobs yet. Create some !'; ?>
	<?php endif ?>

	</table>
</div>
