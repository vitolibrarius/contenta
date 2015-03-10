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
	<li>
		<a href="<?php echo Config::Web( '/AdminPublishers/comicVineSearch' ); ?>"><span class="">ComicVine Import</span></a>
	</li>
	</ul>
</div>

<?php if (is_array($this->list) && count($this->list) > 0 ): ?>
	<?php
		$card = new html\Card();
		$card->setDetailKeys( array(
			model\Publisher::xsource => model\Publisher::xsource
			)
		);
		foreach($this->list as $key => $value) {
			$card->setEditPath( $this->editAction . '/' . $value->id );
			$card->setDeletePath( $this->deleteAction . '/' . $value->id );
			echo $card->render($value);
		}
	?>
<?php else: ?>
	<?php echo 'No publishers yet. Create some !'; ?>
<?php endif ?>
