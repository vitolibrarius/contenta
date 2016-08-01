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
			\model\media\Publication::issue_num => "issue_num",
			\model\media\Publication::pub_date => "publishedMonthYear",
			)
		);
		if ( is_null($this->listArray) || count($this->listArray) == 0) {
			echo "No records";
		}

		foreach($this->listArray as $key => $value) {
			echo '<div class="grid_3">' . PHP_EOL;
			echo $card->render($value, function() use($value) {
					$all_media = $value->media();
					if ( is_array($all_media) ) {
						foreach ($all_media as $idx => $media) {
							$c[] = H::p( $media->formattedSize() );
						}
					}

					$c[] = H::a(
						array(
							"class" => "button",
							"href" => Config::Web($this->saveAction, $value->id)),
						"Select"
					);
					return (isset($c) ? $c : null);
				}
			);
			echo '</div>' . PHP_EOL;
		}
	?>
<?php endif; ?>
	</div>
</section>
