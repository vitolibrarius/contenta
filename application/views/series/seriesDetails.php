<?php use html\Element as H ?>
<?php use \html\ProgressBar as ProgressBar; $progress = new ProgressBar(); ?>

<section>
	<div class="row">
		<div class="grid_1">
			<img class="thumbnail cbz" src="<?php echo Config::Web( "Image", "thumbnail", "series", $this->detail->id); ?>" />
		</div>
		<div class="grid_4">
			<p class="description"><?php echo $this->detail->displayDescription(); ?></p>
			<div class="badges">
				<?php $list = $this->detail->series_artists();
					if ( is_array($list) && count($list) > 0 ): ?>
					<?php
						$roleGroups = array();
						foreach($list as $serArtist) {
							$key = $serArtist->roleName();
							$artistGroups[$key][] = $serArtist->artist();
						}
						ksort($artistGroups); ?>
					<?php foreach ($artistGroups as $role => $artists): ?>
					<div class="<?php echo $role; ?>">
						<h6><?php echo $role; ?></h6>
						<ul class="badge artist">
						<?php foreach ($artists as $artist): ?>
							<li class="artist">
								<?php if ( isset($this->artistAction) ): ?>
									<a href="<?php echo Config::Web($this->artistAction, $artist->id); ?>">
								<?php endif; ?>
								<?php echo $artist->name(); ?>
								<?php if ( isset($this->artistAction) ) :?></a><?php endif; ?>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="grid_4">
			<h6>Reading Progress</h6>
			<?php echo $progress->render($this->detail); ?>
			<div class="badges">
				<?php $list = $this->detail->characters(10);
					if ( is_array($list) && count($list) > 0 ): ?>
					<h6>Characters</h6>
					<ul class="badge characters">
					<?php foreach ($list as $character): ?>
						<li class="character">
							<?php if ( isset($this->characterAction) ): ?>
								<a href="<?php echo Config::Web($this->characterAction, $character->id); ?>">
							<?php endif; ?>
							<?php echo $character->name; ?>
							<?php if ( isset($this->characterAction) ) :?></a><?php endif; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php $list = $this->detail->story_arcs(10);
					if ( is_array($list) && count($list) > 0 ): ?>
					<h6>Story Arcs</h6>
					<ul class="badge story_arc">
					<?php foreach ($list as $story): ?>
						<li class="story_arc">
							<?php if ( isset($this->storyArcAction) ): ?>
								<a href="<?php echo Config::Web($this->storyArcAction, $story->id); ?>">
							<?php endif; ?>
							<?php echo $story->name; ?>
							<?php if ( isset($this->storyArcAction) ) :?></a><?php endif; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="row">
	<?php if (empty($this->detail->publications())): ?>
		<div style="background:hsl(326,50%,75%)">
			There are no publications
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

			foreach($this->detail->publications() as $key => $value) {
				if ( isset($this->editAction) ) {
					$card->setEditPath( $this->editAction . '/' . $value->id );
				}
				if ( isset($this->deleteAction) ) {
					$card->setDeletePath( $this->deleteAction . '/' . $value->id );
				}
				echo '<div class="grid_3">' . PHP_EOL;
				echo $card->render($value, function() use($value) {
							$all_media = $value->media();
							if ( is_array($all_media) ) {
								foreach ($all_media as $idx => $media) {
									$c[] = H::em( $media->formattedSize(),
										H::a( array( "href" => Config::Web("/Api/mediaPayload/" . $media->id)),
												H::img( array( "src" => Config::Web("/public/img/download.png" )))
											),
										H::a( array( "target" => "slideshow", "href" => Config::Web("/DisplayMedia/slideshow/".$media->id)),
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
</section>
