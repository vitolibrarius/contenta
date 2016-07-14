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
		<li><a href="<?php echo Config::Web('/AdminJobs/runningIndex'); ?>">
			<span class=""><?php echo $this->label( "JobsRunningLink", "name" ); ?></span></a>
		</li>
		<li><a href="<?php echo Config::Web('/AdminJobs/edit'); ?>"><span class="">Add New Job</span></a></li>
	</ul>
</div>

<!-- 	const id =			'id';
	const type_code =		'type_code';
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
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "type_code" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "endpoint_id" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "minute" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "hour" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "dayOfWeek" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "lastDate" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "lastFailDate" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "nextDate" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "elapsed" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "one_shot" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "enabled" ); ?></th>
			<th colspan=3></th>
		</tr>
	<?php if (is_array($this->objects)): ?>
		<?php foreach($this->objects as $key => $value): ?>
				<?php
					$runningJobs = \Model::Named("Job_Running")->allForJob($value);
					$running = ( is_array($runningJobs) && count($runningJobs) > 0 );
					$endpointRequired = boolval($value->{"jobType/isRequires_endpoint"}());
					$endpointEnabled = boolval($value->{"endpoint/isEnabled"}());
					$endpointNote = ($endpointRequired
						? $value->{"endpoint/name"}() . ($endpointEnabled ? "" : " (disabled)")
						: Localized::ModelLabel($this->model->tableName(), "EndpointNotRequired" )
					);
				?>
				<tr <?php if ( $running == true ) { echo 'class="blocked"'; } ?> >
					<td><?php echo $value->{"jobType/name"}() . '<br><i>' . $value->jobType()->desc . '</i>'; ?></td>
					<td><?php echo $endpointNote; ?></td>
					<td><?php echo $value->minute; ?></td>
					<td><?php echo $value->hour; ?></td>
					<td><?php echo $value->dayOfWeek; ?></td>
					<td><?php echo $value->formattedDateTime_last_run(); ?></td>
					<td><?php echo $value->formattedDateTime_last_fail(); ?></td>
					<td><?php echo $value->formattedDateTime_next(); ?></td>
					<td><?php echo $value->elapsedFormatted(); ?></td>
					<td><span class="icon <?php echo ($value->isOne_shot() ? 'true' : 'false') ?>"></span></td>
					<td><span class="icon <?php echo ($value->isEnabled() ? 'true' : 'false') ?>"></span></td>

					<td><?php if ( $running == false ): ?>
						<a href="<?php echo Config::Web('/AdminJobs/edit/'. $value->id); ?>"><span class="icon edit" /></a>
						<?php endif; ?>
					</td>
					<td><?php if ( $running == false && $value->enabled && ($endpointRequired == false || $endpointEnabled) ): ?>
						<a href="<?php echo Config::Web('/AdminJobs/execute/'. $value->id); ?>"><span class="icon run" /></a>
						<?php endif; ?>
					</td>
					<td><?php if ( $running == false ): ?>
						<a class="confirm" action="<?php echo Config::Web('/AdminJobs/delete/', $value->id); ?>" href="#">
						<span class="icon recycle"></span></a>
						<?php endif; ?>
					</td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<?php echo 'No jobs yet. Create some !'; ?>
	<?php endif ?>

	</table>
</div>
