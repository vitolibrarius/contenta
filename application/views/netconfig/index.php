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
								<div style="word-wrap: break-word; width:275px" >
									<?php echo $endpoint->base_url; ?>
								</div>
							</td>
						</tr>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "api_key" ); ?></th>
							<td><?php echo $endpoint->api_key; ?></td>
						</tr>
						<tr>
							<th><?php echo Localized::ModelLabel($this->model->tableName(), "username" ); ?></th>
							<td><?php echo $endpoint->username; ?></td>
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
	<?php echo 'No endpoints yet. Create some !'; ?>
<?php endif; ?>
</div>
</section>
