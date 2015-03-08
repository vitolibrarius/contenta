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
			Character::id, Character::publisher_id, Character::name, Character::realname, Character::desc,
			Character::gender, Character::created, Character::popularity,
			Character::path, Character::small_icon_name, Character::large_icon_name,
			Character::xurl, Character::xsource, Character::xid, Character::xupdated
	-->
<div class="mediaData">
	<table>
	<?php if (is_array($this->list) && count($this->list) > 0 ): ?>
		<tr>
			<th></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "realname" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "desc" ); ?></th>
			<th><?php echo Localized::ModelLabel($this->model->tableName(), "gender" ); ?></th>
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
					<td><?php echo $value->realname; ?></td>
					<td><?php echo $value->desc; ?></td>
					<td><?php echo $value->gender; ?></td>
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
			<th colspan="7"><?php echo 'No characters yet. Create some !'; ?></th>
		</tr>
	<?php endif ?>

	<tfoot>
		<tr>
			<td colspan="7">
				<a class="btn" href="<?php echo Config::Web( '/AdminCharacters/comicVineSearch' ); ?>"><span class="">ComicVine Import</span></a>
			</td>
		</tr>
	</tfoot>
	</table>
</div>
