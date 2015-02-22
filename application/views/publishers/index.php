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
	<?php if (is_array($this->list) && count($this->list) > 0 ): ?>
		<tr>
			<th></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "path" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "xsource" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "xurl" ); ?></th>
			<th colspan=2></th>
		</tr>
		<?php foreach($this->list as $key => $value): ?>
			   <tr>
					<td>
						<?php if ( $value->hasIcons() ): ?>
							<img src="<?php echo Config::Web( "Image", "icon", $this->model->tableName(), $value->id); ?>" />
						<?php endif; ?>
					</td>
					<td><?php echo htmlentities($value->name); ?></td>
					<td><?php echo $value->path; ?></td>
					<td><?php echo $value->xsource; ?></td>
					<td><?php echo $value->xurl; ?></td>
					<td><?php if (isset($this->editAction)) : ?>
						<a href="<?php echo Config::Web( $this->editAction . '/' . $value->id); ?>"><span class="icon edit" /></a>
						<?php endif; ?></td>
					<td><?php if (isset($this->deleteAction)) : ?>
						<a class="confirm" href="#" action="<?php echo Config::Web( $this->deleteAction . '/' . $value->id); ?>"><span class="icon recycle" /></a>
						<?php endif; ?></td>
					</td>
				</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<th colspan="7"><?php echo 'No publishers yet. Create some !'; ?></th>
		</tr>
	<?php endif ?>

	<tfoot>
		<tr>
			<td colspan="7">
				<a class="btn" href="<?php echo Config::Web( '/AdminPublishers/comicVineSearch' ); ?>"><span class="">ComicVine Import</span></a>
			</td>
		</tr>
	</tfoot>
	</table>
</div>
