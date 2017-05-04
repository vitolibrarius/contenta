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
			<?php if (isset($item['metadata'], $item['metadata']['name'])) {
					$seriesName = $item['metadata']['name'];
					$issue = (isset($item['metadata']['issue']) ? $item['metadata']['issue'] : null);
					$year = (isset($item['metadata']['year']) ? $item['metadata']['year'] : null);
					$publications = Model::Named("Publication")->publicationsLike($seriesName, $issue, $year);
				}
			?>
		<tr <?php echo ($item['password'] == true ? "class='blocked'" : ""); ?>>
			<td class="name">
				<h4><?php echo $item['title']; ?></h4>
				<p><?php echo date("M d, Y", $item['publishedDate']); ?></p>
				<p><?php echo formatSizeUnits($item['len']); ?></p>
				<?php echo ($item['password'] == true ? "<em>**** password protected</em>" : ""); ?>
			</td>
			<td>
				<?php $flux = $this->fluxModel->objectForSrc_guid( $item['guid'] );
					if ($flux == false ) : ?>
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
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	</div>
<?php else : ?>
	<p style="text-align: center;"><em>No postings on <?php echo $endpoint->name; ?></em></p>
<?php endif; ?>
