<?php if (is_array($this->issue) && count($this->issue) > 0): ?>
<div class="mediaData">
	<table>
		<tr>
			<th class="contenta">Publisher</th>
			<th class="contenta">Existing Series</th>
			<th>Issue</th>
			<th>Published</th>
			<th>Name</th>
			<th></th>
		</tr>
			<?php foreach ($this->issue as $idx => $item): ?>
				<?php
					$series_xid = valueForKeypath( "volume/id", $item );
					$existingSeries = $this->series_model->objectForExternal( $series_xid, model\Endpoint_Type::ComicVine);
				?>

			<tr>
				<?php if ( $existingSeries instanceof model\SeriesDBO ) : ?>
				<td>
					<?php
						$publisher = $existingSeries->publisher();
						if ( $publisher instanceof model\PublisherDBO ) {
							echo '<img src="' .
								Config::Web( "Image", "icon", "publisher", $existingSeries->publisher_id)
								. '" class="thumbnail" />';
							echo '<span class="publisher name">' . $publisher->name . '</span>';
						}
					?>
				</td>

				<td class="name">
					<h3><?php echo $existingSeries->name; ?></h3>
					<?php
						$issue_xid = valueForKeypath( "id", $item );
						$issue = $existingSeries->publicationForExternal( $issue_xid, model\Endpoint_Type::ComicVine);
						if ( $issue instanceof model\PublicationDBO ) {
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
				</td>
				<?php else : ?>
					<td></td>
					<td></td>
				<?php endif; ?>

				<td class="issue"><?php echo $item['issue_number']; ?></td>
				<td class="published">
					<img src="<?php echo $item['image']['thumb_url'] ?>" class="thumbnail" /><br>
					<nobr><?php echo $item['cover_date']; ?></nobr>
				</td>
				<td class="name">
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
				</td>
				<td>
					<a class="btn" href="<?php echo Config::Web('/AdminUploadRepair/comicVine_accept/', $this->key, $item['id']); ?>">Accept</a>
				</td>
			</tr>
			<?php endforeach; ?>
	</table>
</div>

<?php else: ?>
No results found
<?php endif; ?>

