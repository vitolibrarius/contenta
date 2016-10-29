<section>
	<div class="row"><div class="grid_12">
<?php use \html\Paginator as Paginator;
	$p = new Paginator( $this->params, Config::Web('/AdminCharacters/searchCharacters') );
	echo $p->render();
?>
	</div></div>

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
				\model\media\Character::realname => \model\media\Character::realname,
				\model\media\Character::gender => \model\media\Character::gender,
				)
			);
			foreach($this->listArray as $key => $value) {
				if ( isset($this->editAction) ) {
					$card->setEditPath( $this->editAction . '/' . $value->id );
				}
				if ( isset($this->deleteAction) ) {
					$card->setDeletePath( $this->deleteAction . '/' . $value->id );
				}
				echo '<div class="grid_3">' . PHP_EOL;
				echo $card->render($value);
				echo '</div>' . PHP_EOL;
			}
		?>
	<?php endif; ?>
	</div>
</section>
