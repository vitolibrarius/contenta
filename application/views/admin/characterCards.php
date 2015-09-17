	<section>
		<div class="row">
<?php if (empty($this->listArray)): ?>
	<div style="background:hsl(326,50%,75%)">
		There are no matching records
	</div>
<?php else: ?>
	<?php
		$card = new html\Card();
		$card->setDisplayDescriptionKey( "shortDescription" );
		$card->setDetailKeys( array(
			model\Character::realname => model\Character::realname,
			model\Character::gender => model\Character::gender,
			)
		);
		foreach($this->listArray as $key => $value) {
			if ( isset($this->editAction) ) {
				$card->setEditPath( $this->editAction . '/' . $value->id );
			}
			if ( isset($this->deleteAction) ) {
	 			$card->setDeletePath( $this->deleteAction . '/' . $value->id );
			}
			echo '<div class="grid_2">' . PHP_EOL;
			echo $card->render($value);
			echo '</div>' . PHP_EOL;
		}
	?>
<?php endif; ?>
		</div>
	</section>
