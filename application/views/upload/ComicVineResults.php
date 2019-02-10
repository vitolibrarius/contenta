<?php if (is_array($this->issue) && count($this->issue) > 0): ?>

<?php
	$existingGroups = array();
	$unknownGroups = array();
	$map = array();
	foreach ($this->issue as $idx => $item) {
		$series_xid = array_valueForKeypath( "volume/id", $item );
		$seriesObj = $this->series_model->objectForExternal( $series_xid, \model\network\Endpoint_Type::ComicVine);

		if ( $seriesObj instanceof \model\media\SeriesDBO ) {
			$map[$series_xid] = $seriesObj;
			$existingGroups[$series_xid][] = $item;
		}
		else {
			$unknownGroups[] = $item;
		}
	}
?>

<?php if ( count($map) > 0 ): ?>
<section>
	<h3>Matches with existing series</h3>
	<div class="row">
<?php foreach ($map as $series_xid => $seriesObj): ?>
<?php
	$searchHits = $existingGroups[$series_xid];
	$publisher = $seriesObj->publisher();
?>
	<?php foreach ($searchHits as $idx => $item): ?>
	<div class="grid_4">
		<figure class="card">
			<div class="figure_top">
				<div class="figure_image">
					<?php if ( isset($item['image'], $item['image']['thumb_url'])) : ?>
						<img src="<?php echo $item['image']['thumb_url'] ?>" class="thumbnail" />
					<?php endif; ?>
					<br>
				</div>
				<div class="figure_details">
					<div class="figure_detail_top">
						<?php if ( is_null($publisher) == false ) {
							echo '<img src="' . Config::Web( "Image", "icon", "publisher", $publisher->id) . '" class="thumbnail" /> ';
							echo '<span class="publisher name">' . $publisher->name . '</span>';
						} ?>
						<h3>
							<?php if ( isset($item['site_detail_url'])) : ?>
								<a target="comicvine" href="<?php echo $item['site_detail_url']; ?>">
									<img class="icon" src="<?php echo Model::Named('Endpoint_Type')->ComicVine()->favicon_url; ?>"
										alt="ComicVine">
								</a>
							<?php endif; ?>
							<?php echo (isset($item['volume'], $item['volume']['name']) ? $item['volume']['name'] : ""); ?>
						</h3>
						<p class="property issue_num"><?php echo $item['issue_number']; ?></p>
						<p class="property pub_date"><?php echo (isset($item['cover_date']) ? $item['cover_date'] : ""); ?></p>
					</div>
					<div class="figure_detail_middle">
						<?php
							$issue_xid = array_valueForKeypath( "id", $item );
							$issue = $seriesObj->publicationForExternal( $issue_xid, \model\network\Endpoint_Type::ComicVine);
							if ( $issue instanceof \model\media\PublicationDBO ) {
								$mediaList = $issue->media();
								if ( is_array($mediaList) && count($mediaList)  ) {
									echo "<table>";
									foreach( $mediaList as $idx => $media ) {
										echo "<tr><td>" . $media->mediaType()->code
											. "</td><td>" . $media->formattedSize()
											. "</td></tr>";
									}
									echo "</table>";
								}
								else {
									echo "<em>No media for this issue</em>";
								}
							}
							else {
								echo "New Issue";
							}
						?>
					</div>
				</div>
			</div>
			<figcaption class="caption">
				<h4><?php echo (isset($item['name']) ? $item['name'] : ""); ?></h4>
				<div style="min-height: 100px; height: 100px; overflow-y : scroll;"><?php
					if ( isset($item['deck']) && strlen($item['deck']) > 0) { echo strip_tags($item['deck']); }
					else if (isset($item['description']) ) { echo strip_tags($item['description']); }
				?></div>

				<a class="button" href="<?php echo Config::Web('/AdminUploadRepair/comicVine_accept/', $this->key, $item['id']); ?>">
					Import Match
				</a>
			</figcaption>
		</figure>
	</div>
	<?php endforeach; ?>
<?php endforeach; ?>
	</div>
</section>
<hr>
<?php endif; ?>


<?php if ( count($unknownGroups) > 0 ): ?>
<section>
	<h3>Matches for new series (not tracking)</h3>
	<div class="row">
<?php foreach ($unknownGroups as $idx => $item): ?>
	<div class="grid_4">
		<figure class="card">
			<div class="figure_top">
				<div class="figure_image">
					<?php if ( isset($item['image'], $item['image']['thumb_url'])) : ?>
						<img src="<?php echo $item['image']['thumb_url'] ?>" class="thumbnail" />
					<?php endif; ?>
					<br>
				</div>
				<div class="figure_details">
					<div class="figure_detail_top">
						<h3>
							<?php if ( isset($item['volume'], $item['volume']['site_detail_url'])) : ?>
								<a target="comicvine" href="<?php echo $item['volume']['site_detail_url']; ?>">
									<img class="icon" src="<?php echo Model::Named('Endpoint_Type')->ComicVine()->favicon_url; ?>"
										alt="ComicVine">
								</a>
							<?php endif; ?>
							<?php echo (isset($item['volume'], $item['volume']['name']) ? $item['volume']['name'] : ""); ?>
						</h3>
						<p class="property issue_num"><?php echo $item['issue_number']; ?></p>
						<p class="property pub_date"><?php echo $item['cover_date']; ?></p>
					</div>
					<div class="figure_detail_middle">
					</div>
				</div>
			</div>
			<figcaption class="caption">
				<h4><?php echo (isset($item['name']) ? $item['name'] : ""); ?></h4>
				<div style="min-height: 100px; height: 100px; overflow-y : scroll;"><?php
					if ( isset($item['deck']) && strlen(trim($item['deck'])) > 0) { echo 'deck' .strip_tags($item['deck']); }
					else if (isset($item['description']) ) { echo strip_tags($item['description']); }
				?></div>

				<a class="button" href="<?php echo Config::Web('/AdminUploadRepair/comicVine_accept/', $this->key, $item['id']); ?>">
					Import Match
				</a>
			</figcaption>
		</figure>
	</div>
<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<?php else: // has issues ?>
No results found
<?php endif; ?>
