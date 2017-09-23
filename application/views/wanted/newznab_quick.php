<?php
	$endpointType = null;
	$endpoint = null;
	if ( isset($this->endpoint_id) ) {
		$endpoint = Model::Named( 'Endpoint' )->objectForId($this->endpoint_id);
		if ( $endpoint ) {
			$endpointType = $endpoint->endpointType();
		}
	}
?>
<?php if (isset($this->results) && count($this->results) > 0): ?>
	<div class="mediaData">
	<table width="100%">
		<tr>
			<th>Postings from <?php echo $endpoint->name; ?></th>
			<th>Status</th>
		</tr>
		<?php foreach ($this->results as $key => $item) : ?>
			<?php
				$seriesNameMatch = "wrong";
				$issueMatch = "wrong";
				$yearMatch = "wrong";
				$sizeMatch = "wrong";
				$pubMatch = "wrong";
				if (isset($item['metadata'], $item['metadata']['name'])) {
					$seriesName = $item['metadata']['name'];
					$issue = (isset($item['metadata']['issue']) ? $item['metadata']['issue'] : null);
					$year = (isset($item['metadata']['year']) ? $item['metadata']['year'] : null);
				}
				if ( is_null($seriesName) == false ) {
					similar_text(strtolower($seriesName), strtolower($this->publication->seriesName()), $percent);
					if ( $percent > 90 ) { $seriesNameMatch = "match"; }
					else if ( $percent > 75 ) { $seriesNameMatch = "close"; }
				}
				if ( is_null($issue) == false ) {
					similar_text($issue, $this->publication->issue_num(), $percent);
					if ( $percent > 90 ) { $issueMatch = "match"; }
					else if ( $percent > 75 ) { $issueMatch = "close"; }
				}
				if ( is_null($year) == false ) {
					similar_text($year, $this->publication->publishedYear(), $percent);
					if ( $percent > 90 ) { $yearMatch = "match"; }
					else if ( $percent > 75 ) { $yearMatch = "close"; }
				}
				if ($item['len'] > (MEGABYTE * 10)) {
					$sizeMatch = "close";
					if ($item['len'] > (MEGABYTE * 25) && $item['len'] <= (MEGABYTE * 75)) {
						$sizeMatch = "match";
					}
					else if ($item['len'] > (MEGABYTE * 75) && $item['len'] <= (MEGABYTE * 150)) {
						$sizeMatch = "bigsize";
					}
					else if ($item['len'] > (MEGABYTE * 150)) {
						$sizeMatch = "yikes";
					}
				}
				if (isset($this->publication->pub_date)) {
					if ( $item['publishedDate'] > $this->publication->pub_date ) {
						$pubMatch = "match";
					}
					else if ( ($this->publication->pub_date - $item['publishedDate']) < 15768000 ) {
						$pubMatch = "close";
					}
				}
				else {
					$pubMatch = "close";
				}
			?>
		<tr <?php echo ($item['password'] == true ? "class='blocked'" : ""); ?>>
			<td class="name">
				<h4><a href="#" class="togglable">
					<div class="toggle_item" id="title_<?php echo $this->publication_id . "_" . $item['safe_guid']; ?>" style="display:none;">
						<span class="nzbsearch title"><?php echo $item['title']; ?></span>
					</div>
					<div class="toggle_item" id="meta_<?php echo $this->publication_id . "_" . $item['safe_guid']; ?>">
						<span class="nzbsearch seriesName <?php echo $seriesNameMatch; ?>"><?php echo $seriesName; ?></span>
						<span class="nzbsearch issue <?php echo $issueMatch; ?>"><?php echo $issue; ?></span>
						<span class="nzbsearch year <?php echo $yearMatch; ?>"><?php echo $year;?></span>
					</div>
				</a></h4>
				<div>
					<p class="nzbsearch pub_date <?php echo $pubMatch; ?>">
						<?php echo date("M d, Y", $item['publishedDate']); ?>
					</p>
				</div>
				<div>
					<p class="nzbsearch size <?php echo $sizeMatch; ?>">
						<?php echo formatSizeUnits($item['len']);?></p>
					</p>
				</div>
				<?php echo ($item['password'] == true ? "<em>**** password protected</em>" : ""); ?>
			</td>
			<td>
				<?php if ($endpoint->isOverMaximum('daily_dnld_max') == false) : ?>
				<?php $flux = $this->fluxModel->objectForSrc_guid( $item['guid'] );
					if ($flux == false) : ?>
						<div id="dnld_<?php echo $this->publication_id . "_" . $item['safe_guid']; ?>">
						<a href="#" class="nzb button" style="white-space:nowrap;"
							data-name="<?php echo htmlentities($seriesName); ?>"
							data-issue="<?php echo $issue; ?>"
							data-year="<?php echo $year; ?>"
							data-endpoint_id="<?php echo $this->endpoint_id; ?>"
							data-guid="<?php echo $item['guid']; ?>"
							data-url="<?php echo $item['url']; ?>"
							data-postedDate="<?php echo $item['publishedDate']; ?>"
							data-ref_guid="dnld_<?php echo $this->publication_id . "_" . $item['safe_guid']; ?>"
							>Download</a>
						</div>
					<?php else: ?>
					<div>
						<div style="white-space: nowrap;">
							<span class="icon <?php echo ($flux->isSourceComplete()?'true':'false'); ?>"></span>
							<?php echo $flux->src_status ; ?>
						</div>
						<?php if ($flux->isSourceComplete() == false): ?>
							<div id="dnld_<?php echo $this->publication_id . "_" . $item['safe_guid']; ?>">
							<a href="#" class="nzb button" style="white-space:nowrap;"
								data-name="<?php echo htmlentities($seriesName); ?>"
								data-issue="<?php echo $issue; ?>"
								data-year="<?php echo $year; ?>"
								data-endpoint_id="<?php echo $this->endpoint_id; ?>"
								data-guid="<?php echo $item['guid']; ?>"
								data-url="<?php echo $item['url']; ?>"
								data-postedDate="<?php echo $item['publishedDate']; ?>"
								data-ref_guid="dnld_<?php echo $this->publication_id . "_" . $item['safe_guid']; ?>"
								>Try Again</a>
							</div>
						<?php else: ?>
							<div style="white-space: nowrap;">
								<span class="icon <?php echo ($flux->isFlux_error()?'false':'true'); ?>"></span>
								<span class="break-word"><?php echo $flux->dest_status ; ?></span>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php else : ?>
					<div style="white-space: nowrap;">
						<span class="icon false"></span>
						<span class="break-word">Over Daily Maximum</span>
					</div>
					<div style="white-space: nowrap;">
						<span class="icon false"></span>
						<span class="break-word"><?php echo $endpoint->dailyMaximumStatus(); ?></span>
					</div>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	</div>
<?php else : ?>
	<p style="text-align: center;"><em>No postings on <?php echo $endpoint->name; ?></em></p>
<?php endif; ?>
