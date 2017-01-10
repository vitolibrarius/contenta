<?php use html\Element as H ?>
<section>
	<div class="row"><div class="grid_12">
<?php use \html\Paginator as Paginator;
	if ( isset($this->params) ) {
		$p = new Paginator( $this->params, Config::Web('/AdminPublishers/publisherList') );
		echo $p->render();
	}
	else {
		echo "<b> no parameters</b>";
	}
?>
	</div></div>
</section>

<section>
	<div class="row">

<?php if (is_array($this->list) && count($this->list) > 0 ): ?>
	<?php
		$card = new html\Card();
		$card->setDetailKeys( array(
			\model\media\Publisher::xsource => \model\media\Publisher::xsource
			)
		);
		foreach($this->list as $key => $value) {
			$card->setEditPath( $this->editAction . '/' . $value->id );
			if ( isset( $this->deleteAction ) ) {
				$card->setDeletePath( $this->deleteAction . '/' . $value->id );
			}
			echo '<div class="grid_3">' . PHP_EOL;
			echo $card->render($value, null, function() use($value) {
				$characters = $value->characters(10);
				$ul = H::ul( array("class" => "badge characters"));
				$div = H::div( array("class" => "badges"), $ul );

				if ( is_array($characters) ) {
					foreach ($characters as $idx => $s) {
						$ul->addElement( H::li( array("class" => "character"), $s->displayName()));
					}
				}
				return $div;
			});
			echo '</div>' . PHP_EOL;
		}
	?>
<?php else: ?>
	<?php echo 'No publishers yet. Create some !'; ?>
<?php endif ?>

	</div>
</section>
