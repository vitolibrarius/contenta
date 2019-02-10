<?php use html\Element as H ?>

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
			\model\media\Book::author => \model\media\Book::author
			)
		);
		foreach($this->listArray as $key => $value) {

			echo '<div class="grid_3">' . PHP_EOL;
			echo $card->render($value, function() use($value) {
					$c[] = H::em( $value->formattedSize(),
						H::a( array( "href" => Config::Web("/Api/bookPayload/" . $value->id)),
							H::img( array( "src" => Config::Web("/public/img/download.png" )))
						)
					);
					return (isset($c) ? $c : null);
				}
			);
			echo '<a class="button" href="' . Config::Web("/Api/bookPayload/" . $value->id) . '">Download</a></div>' . PHP_EOL;
		}
	?>
<?php endif; ?>
	</div>
</section>
