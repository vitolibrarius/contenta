<?php use html\Element as H ?>

<div class="series_wrapper">
	<div class="series sidebar">
		<img class="thumbnail cbz" src="<?php echo Config::Web( "Image", "thumbnail", "series", $this->detail->id); ?>" /><br>
		<div class="actions">
		</div>

		<div class="description"><?php echo $this->detail->shortDescription(); ?></div>

		<ul class="badge characters">
		<?php foreach ($this->detail->characters(10) as $character): ?>
			<li class="character"><?php echo $character->name; ?></li>
		<?php endforeach; ?>
		</ul>
	</div>


<?php if (empty($this->detail->publications())): ?>
	<div style="background:hsl(326,50%,75%)">
		There are no publications
	</div>
<?php else: ?>
	<?php
		$card = new html\Card();
		$card->setDisplayDescriptionKey( "shortDescription" );
		$card->setDetailKeys( array(
			model\Publication::issue_num => "issue_num",
			model\Publication::pub_date => "publishedMonthYear",
			)
		);

		foreach($this->detail->publications() as $key => $value) {
			if ( isset($this->editAction) ) {
				$card->setEditPath( $this->editAction . '/' . $value->id );
			}
			if ( isset($this->deleteAction) ) {
	 			$card->setDeletePath( $this->deleteAction . '/' . $value->id );
			}
			echo $card->render($value, function() use($value) {
						$all_media = $value->media();
						if ( is_array($all_media) ) {
							foreach ($all_media as $idx => $media) {
								$c[] = H::a( array( "href" => Config::Web("/Api/mediaPayload/" . $media->id)),
											H::img( array( "src" => Config::Web("/public/img/download.png" )))
										);
								$c[] = H::a( array( "target" => "slideshow", "href" => Config::Web("/DisplaySeries/mediaSlideshow/".$media->id)),
											H::img( array( "src" => Config::Web("/public/img/slideshow.png") ))
										);
							}
						}
						return (isset($c) ? $c : null);
					}
				);
		}
	?>
<?php endif; ?>
</div>
