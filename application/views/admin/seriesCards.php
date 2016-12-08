<?php use html\Element as H ?>
<section>
	<div class="row"><div class="grid_12">
<?php use \html\Paginator as Paginator;
	if ( isset($this->params) ) {
		$p = new Paginator( $this->params, Config::Web('/AdminSeries/searchSeries') );
		echo $p->render();
	}
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
				\model\media\Series::start_year => \model\media\Series::start_year,
				\model\media\Series::pub_count => "availableSummary",
				)
			);
			foreach($this->listArray as $key => $value) {
				if ( isset($this->editAction) ) {
					$card->setEditPath( $this->editAction . '/' . $value->id );
					$card->setSelectPath( $this->editAction . '/' . $value->id );
				}
				if ( isset($this->selectAction) ) {
					$card->setSelectPath( $this->selectAction . '/' . $value->id );
				}
				if ( isset($this->deleteAction) ) {
					$card->setDeletePath( $this->deleteAction . '/' . $value->id );
				}
				if ( isset($this->wantedAction) ) {
					$card->setWantedPath( $this->wantedAction . '/' . $value->id );
				}
				echo '<div class="grid_3">' . PHP_EOL;
				echo $card->render($value);
				echo '</div>' . PHP_EOL;
			}
		?>
	<?php endif; ?>
	</div>
</section>
