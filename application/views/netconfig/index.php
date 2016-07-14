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

<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "enabled" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "type_code" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "base_url" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "api_key" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "username" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "compressed" ); ?></th>
			<th colspan=2></th>
		</tr>
	<?php if (is_array($this->endpoints)): ?>
		<?php foreach($this->endpoints as $key => $value): ?>
			   <tr>
					<td><span class="icon <?php echo ($value->enabled ? 'true' : 'false') ?>"></span></td>
					<td><?php echo $value->endpointType()->name; ?></td>
					<td><?php echo htmlentities($value->name); ?></td>
					<td><?php echo $value->base_url; ?></td>
					<td><?php echo $value->api_key; ?></td>
					<td><?php echo $value->username; ?></td>
					<td><span class="icon <?php echo ($value->compressed ? 'true' : 'false') ?>"></span></td>
					<td><a href="<?php echo Config::Web('/netconfig/edit/'. $value->id); ?>"><span class="icon edit" /></a></td>
					<td><a class="confirm" action="<?php echo Config::Web('/netconfig/delete/') . $value->id; ?>" href="#">
						<span class="icon recycle"></span></a>
					</td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<?php echo 'No endpoints yet. Create some !'; ?>
	<?php endif ?>

	</table>
</div>
