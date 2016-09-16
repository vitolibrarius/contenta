<?php use html\Element as H ?>
<div class="row">
<?php if (empty($this->listArray)): ?>
	<div style="background:hsl(326,50%,75%)">
		There are no matching records
	</div>
<?php else: ?>
	<?php
		$card = new html\Card();
		$card->setThumbnailTable( "publication" );
		$card->setThumbnailPrimaryKeypath( "reading_item/publication/id" );
		$card->setDisplayNameKey( "reading_item/publication/name" );
		$card->setDisplayDescriptionKey( "reading_item/publication/shortDescription" );
		$card->setDetailKeys( array(
			\model\media\Publication::series_id => "reading_item/publication/series/name",
			\model\media\Publication::issue_num => "reading_item/publication/issue_num",
			\model\media\Publication::pub_date => "reading_item/publication/publishedMonthYear",
			)
		);
		if ( isset($this->readItemPath) ) {
			$card->setReadItemPath( $this->readItemPath );
		}

		foreach($this->listArray as $key => $value) {
			echo '<div class="grid_3">' . PHP_EOL;
			echo $card->render($value, function() use($value) {
					$item = $value->reading_item();
					$publication = $item->publication();
					$all_media = $publication->media();
					if ( is_array($all_media) ) {
						foreach ($all_media as $idx => $media) {
							$c[] = H::em( $media->formattedSize(),
								H::a( array( "href" => Config::Web("/Api/mediaPayload/" . $media->id)),
										H::img( array( "src" => Config::Web("/public/img/download.png" )))
									),
								H::a( array( "target" => "slideshow", "href" => Config::Web("/DisplaySeries/mediaSlideshow/".$media->id)),
									H::img( array( "src" => Config::Web("/public/img/slideshow.png") ))
									)
							);
						}
					}
					return (isset($c) ? $c : null);
				}
			);
			echo '</div>' . PHP_EOL;
		}
	?>
<?php endif; ?>
</div>
