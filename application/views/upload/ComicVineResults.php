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

<section>

<?php foreach ($map as $series_xid => $seriesObj): ?>
<?php
	$searchHits = $existingGroups[$series_xid];
	$publisher = $seriesObj->publisher();
?>
	<div class="row">
		<div class="grid_2">
			<?php if ( is_null($publisher) == false ) {
				echo '<img src="' . Config::Web( "Image", "icon", "publisher", $publisher->id) . '" class="thumbnail" /> ';
				echo '<span class="publisher name">' . $publisher->name . '</span>';
			} ?>
		</div>
		<div class="grid_10">
			<h2>Existing Content for <em><?php echo $seriesObj->name; ?></em></h2>
		</div>
	</div>

	<?php foreach ($searchHits as $idx => $item): ?>
		<div class="row">
			<div class="grid_2">
				<?php
					$issue_xid = array_valueForKeypath( "id", $item );
					$issue = $seriesObj->publicationForExternal( $issue_xid, \model\network\Endpoint_Type::ComicVine);
					if ( $issue instanceof \model\media\PublicationDBO ) {
						echo '<h4 class="publication name">' . $issue->name . '</h4>';
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
			<div class="grid_1">
				<?php echo $item['issue_number']; ?>
			</div>
			<div class="grid_1">
				<img src="<?php echo $item['image']['thumb_url'] ?>" class="thumbnail" /><br>
				<nobr><?php echo $item['cover_date']; ?></nobr>
			</div>
			<div class="grid_4">
				<h3>
					<a target="comicvine" href="<?php echo $item['site_detail_url']; ?>">
						<img class="icon" src="<?php echo Model::Named('Endpoint_Type')->ComicVine()->favicon_url; ?>"
							alt="ComicVine">
					</a>
					<?php echo $item['volume']['name']; ?>
				</h3>
				<h4><?php echo $item['name']; ?></h4>
				<span><?php
					if ( isset($item['deck']) && strlen($item['deck']) > 0) { echo $item['deck']; }
					else if (isset($item['description']) ) { echo $item['description']; }
				?></span>
			</div>
			<div class="grid_1">
				<a class="button" href="<?php echo Config::Web('/AdminUploadRepair/comicVine_accept/', $this->key, $item['id']); ?>">Accept</a>
			</div>
		</div>

	<?php endforeach; ?>

<?php endforeach; ?>

<?php if ( count($unknownGroups) > 0 ): ?>
	<div class="row">
		<div class="grid_2">
		</div>
		<div class="grid_10">
			<h2>New Content</h2>
		</div>
	</div>

	<?php foreach ($unknownGroups as $idx => $item): ?>
		<div class="row">
			<div class="grid_2">
			</div>
			<div class="grid_1">
				<?php echo $item['issue_number']; ?>
			</div>
			<div class="grid_1">
				<img src="<?php echo $item['image']['thumb_url'] ?>" class="thumbnail" /><br>
				<nobr><?php echo $item['cover_date']; ?></nobr>
			</div>
			<div class="grid_4">
				<h3>
					<a target="comicvine" href="<?php echo $item['site_detail_url']; ?>">
						<img class="icon" src="<?php echo Model::Named('Endpoint_Type')->ComicVine()->favicon_url; ?>"
							alt="ComicVine">
					</a>
					<?php echo $item['volume']['name']; ?>
				</h3>
				<h4><?php if ( isset($item['name']) ) { echo $item['name']; } ?></h4>
				<span><?php
					if ( isset($item['deck']) && strlen($item['deck']) > 0) { echo $item['deck']; }
					else if (isset($item['description']) ) { echo $item['description']; }
				?></span>
			</div>
			<div class="grid_1">
				<a class="button" href="<?php echo Config::Web('/AdminUploadRepair/comicVine_accept/', $this->key, $item['id']); ?>">Accept</a>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

<?php else: ?>
No results found
<?php endif; ?>

</section>
