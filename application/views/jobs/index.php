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

<?php if (is_array($this->objects) && count($this->objects) > 0): ?>
<?php
	$jobGroups = array();
	foreach($this->objects as $job) {
		$endpointRequired = boolval($job->{"jobType/isRequires_endpoint"}());
		$group = Localized::ModelLabel($this->model->tableName(), "EndpointNotRequired" );
		if ( $endpointRequired == true ) {
			$endpoint = $job->endpoint();
			if ( $endpoint == false ) {
				$group = Localized::ModelLabel($this->model->tableName(), "EndpointMissing" );
			}
			else {
				$group = $endpoint->type_code;
			}
		}
		$jobGroups[$group][] = $job;
	}
	ksort($jobGroups);
?>
<?php foreach($jobGroups as $group => $jobArray): ?>
	<h3><?php echo $group; ?></h3>
	<div class="row">
		<?php foreach($jobArray as $key => $job): ?>
		<?php
			$runningJobs = \Model::Named("Job_Running")->allForJob($job);
			$running = ( is_array($runningJobs) && count($runningJobs) > 0 );
			$endpointRequired = boolval($job->{"jobType/isRequires_endpoint"}());
			$endpointEnabled = boolval($job->{"endpoint/isEnabled"}());
			$endpointNote = ($endpointRequired
				? $job->{"endpoint/name"}() . ($endpointEnabled ? "" : " (disabled)")
				: Localized::ModelLabel($this->model->tableName(), "EndpointNotRequired" )
			);
		?>

		<div class="grid_4">
		<figure class="card">
			<div class="figure_top <?php if ( $running == true ) { echo 'blocked'; } ?>">
				<div class="figure_image" style="min-height: 0">
					<h3><?php echo $job->{"jobType/name"}(); ?></h3>
				</div>
					<div class="figure_detail_top">
						<?php echo ($job->isEnabled() ? 'Enabled' : 'Disabled') ?>
						<span class="icon <?php echo ($job->isEnabled() ? 'true' : 'false') ?>"></span>
						<?php if ( $running == true ): ?>
							<p><em>Running</em></p>
						<?php endif; ?>
					</div>

				<br>
				<p style="text-align: center;">
					<?php if ( $running == false ): ?>
						<a style="padding: 1em;" href="<?php echo Config::Web('/AdminJobs/edit/'. $job->id); ?>"><span class="icon edit" /></a>
						<?php if ( $job->enabled && ($endpointRequired == false || $endpointEnabled) ): ?>
							<a style="padding: 1em;" href="<?php echo Config::Web('/AdminJobs/execute/'. $job->id); ?>"><span class="icon run" /></a>
						<?php endif; ?>
						<a style="padding: 1em;" class="confirm" action="<?php echo Config::Web('/AdminJobs/delete/', $job->id); ?>" href="#">
							<span class="icon recycle"></span></a>
					<?php endif; ?>
				</p>
			</div>
			<figcaption class="caption">
				<p><em><?php echo $endpointNote; ?></em></p>
				<div class="mediaData">
					<table width="100%">
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "schedule" ); ?></th>
							<td><?php echo $job->minute(); ?></td>
							<td><?php echo $job->hour(); ?></td>
							<td><?php echo $job->dayOfWeek(); ?></td>
						</tr>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "lastDate" ); ?></th>
							<td colspan="3"><?php echo $job->formattedDateTime_last_run(); ?></td>
						</tr>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "elapsed" ); ?></th>
							<td colspan="3"><?php echo $job->elapsedFormatted(); ?></td>
						</tr>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "lastFailDate" ); ?></th>
							<td colspan="3"><?php echo $job->formattedDateTime_last_fail(); ?></td>
						</tr>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "nextDate" ); ?></th>
							<td colspan="3">
								<p>
								<?php $nrdates = $job->nextDates(3); echo implode( "</p><p>", $nrdates ); ?>
								</p>
							</td>
						</tr>

					</table>
				</div>

			</figcaption>
		</figure>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
<?php else: ?>
	<?php echo 'No jobs yet. Create some !'; ?>
<?php endif; ?>
