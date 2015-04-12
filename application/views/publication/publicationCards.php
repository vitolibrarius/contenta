<?php if (empty($this->listArray)): ?>
	<div style="background:hsl(326,50%,75%)">
		There are no matching records
	</div>
<?php else: ?>
	<?php
		$card = new html\Card();
		$card->setDetailKeys( array(
			)
		);
		if ( is_null($this->listArray) || count($this->listArray) == 0) {
			echo "No records";
		}

		foreach($this->listArray as $key => $value) {
			if ( isset($this->editAction) ) {
				$card->setEditPath( $this->editAction . '/' . $value->id );
			}
			if ( isset($this->deleteAction) ) {
	 			$card->setDeletePath( $this->deleteAction . '/' . $value->id );
			}
			echo $card->render($value);
		}
	?>
<?php endif; ?>
