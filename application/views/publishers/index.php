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
	<!--
			Publisher::id, Publisher::name, Publisher::created, Publisher::updated,
			Publisher::path, Publisher::small_icon_name, Publisher::large_icon_name,
			Publisher::xurl, Publisher::xsource, Publisher::xid, Publisher::xupdated
	-->
<div class="mediaData">
	<table>
		<tr>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "path" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "xsource" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "xurl" ); ?></th>
			<th colspan=2></th>
		</tr>
	<?php if (is_array($this->list)): ?>
		<?php foreach($this->list as $key => $value): ?>
			   <tr>
					<td><?php echo htmlentities($value->name); ?></td>
					<td><?php echo $value->path; ?></td>
					<td><?php echo $value->xsource; ?></td>
					<td><?php echo $value->xurl; ?></td>
					<td><a href="<?php echo Config::Web('/AdminPublishers/edit_publisher/'. $value->id); ?>"><span class="icon edit" /></a></td>
					<td><a class="confirm" action="<?php echo Config::Web('/AdminPublishers/delete_publisher/') . $value->id; ?>" href="#">
						<span class="icon recycle"></span></a>
					</td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<?php echo 'No publishers yet. Create some !'; ?>
	<?php endif ?>

	</table>
</div>
