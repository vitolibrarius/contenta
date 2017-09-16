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
		<li><a href="<?php echo Config::Web('/netconfig/edit'); ?>"><span class="">Add New Endpoint</span></a></li>
	</ul>
</div>

<?php if (is_array($this->endpoints) && count($this->endpoints) > 0): ?>
<?php
	$groups = array();
	foreach($this->endpoints as $endpoint) {
		$group = $endpoint->endpointType()->name;
		$groups[$group][] = $endpoint;
	}
	ksort($groups);
?>

<section>
<div class="wrapper">
<?php foreach($groups as $group => $endpointArray): ?>
	<div class="row data">
		<div class="grid_12"><h2><?php echo $group; ?></h2></div>
	</div>
	<div class="row">
		<?php foreach($endpointArray as $key => $endpoint): ?>
		<div class="grid_4">
		<figure class="card">
			<div class="figure_top">
				<div class="figure_image" style="min-height: 0">
					<img src="<?php echo $endpoint->endpointType()->favicon(); ?>">
					<h3><?php echo $endpoint->name(); ?></h3>
				</div>
					<div class="figure_detail_top">
						<?php echo ($endpoint->isEnabled() ? 'Enabled' : 'Disabled') ?>
						<span class="icon <?php echo ($endpoint->isEnabled() ? 'true' : 'false') ?>"></span>
					</div>

				<br>
				<p style="text-align: center;">
					<a href="<?php echo Config::Web('/netconfig/edit/'. $endpoint->id); ?>"><span class="icon edit" /></a>

					<a class="confirm" action="<?php echo Config::Web('/netconfig/delete/', $endpoint->id); ?>" href="#">
						<span class="icon recycle"></span></a>
				</p>
			</div>
			<figcaption class="caption">
				<div class="mediaData">
					<table width="100%">
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "base_url" ); ?></th>
							<td>
								<div >
									<?php echo $endpoint->base_url; ?>
								</div>
							</td>
						</tr>

						<?php if ( isset($endpoint->api_key) && strlen($endpoint->api_key) > 0 ) : ?>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "api_key" ); ?></th>
							<td><?php echo $endpoint->api_key; ?></td>
						</tr>
						<?php endif; ?>

						<?php if ( isset($endpoint->daily_max) && intval($endpoint->daily_max) > 0) : ?>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "daily_max" ); ?></th>
							<td><?php echo $endpoint->dailyMaximumStatus(); ?></td>
						</tr>
						<?php endif; ?>

						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "jobs" ); ?></th>
							<?php $jobs = $endpoint->jobs(); if ( is_array($jobs) && count($jobs) > 0) : ?>
								<td>
									<table width="100%">
										<tr><th></th><th>Type</th><th>Failed</th><th>Next</th></tr>
									<?php
									foreach( $jobs as $idx => $j ) {
										echo '<tr><td><span class="icon ' . ($j->isEnabled() ? 'true' : 'false') . '"></span></td>'
										. '<td><p>' . $j->{"jobType/name"}() . '</p></td>'
										. '<td><p>' . ($j->last_fail ? $j->formattedDateTime_last_fail() : "") . '</p></td>'
										. '<td><p>' . $j->nextDate() . '</p></td>'
										.'</tr>';
									} ?>
									</table>
								</td>
							<?php else : ?>
								<td><em>No scheduled jobs</em></td>
							<?php endif; ?>
						</tr>

						<?php if ( $endpoint->endpointType() && $endpoint->endpointType()->isRSS() ) : ?>
						<tr>
							<th>Activity</th>
							<td>
								<?php $activity = $endpoint->rssCount();
								foreach( $activity as $age => $count ) {
									echo '<p>' . $count . ' in the last ' . formattedTimeElapsed($age) . '</p>';
								} ?></td>
						</tr>
						<?php endif; ?>


						<?php if ( $endpoint->endpointType() && $endpoint->endpointType()->isSABnzbd() ) : ?>
						<tr>
							<th>Activity</th>
							<td>
								<?php $activity = $endpoint->fluxDestCount();
								foreach( $activity as $age => $counts ) {
									if ( is_array( $counts ) && count($counts) > 0 ) {
										echo '<p>';
										foreach ( $counts as $idx => $status ) {
											$count = $status->count;
											$dest_status = $status->dest_status;
											echo $count . ' ' . $dest_status . ($idx == 0 ? ", " : "");
										}
										echo ' in the last ' . formattedTimeElapsed($age) . '</p>';
									}
								}

								//echo '<pre>'.json_encode($activity, JSON_PRETTY_PRINT).'</pre>';
								?></td>
						</tr>
						<?php endif; ?>

						<?php if ( $endpoint->endpointType() && $endpoint->endpointType()->isNewznab() ) : ?>
						<tr>
							<th>Activity</th>
							<td>
								<?php $activity = $endpoint->fluxSrcCount();
								foreach( $activity as $age => $counts ) {
									if ( is_array( $counts ) && count($counts) > 0 ) {
										echo '<p>';
										foreach ( $counts as $idx => $status ) {
											$count = $status->count;
											$src_status = $status->src_status;
											echo $count . ' ' . $src_status . ($idx == 0 ? ", " : "");
										}
										echo ' in the last ' . formattedTimeElapsed($age) . '</p>';
									}
								}

								//echo '<pre>'.json_encode($activity, JSON_PRETTY_PRINT).'</pre>';
								?></td>
						</tr>
						<?php endif; ?>

					</table>
				</div>

			</figcaption>
		</figure>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
<?php else: ?>
	<?php echo 'No endpoints yet. Create some !'; ?>
<?php endif; ?>
</div>
</section>
