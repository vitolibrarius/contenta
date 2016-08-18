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
<section>
    <div class="wrapper">
		<div class="row">
			<div class="grid_12">
<?php if ( isset($endpoint, $endpoint->name) ) : ?>
<div class="mediaData">
	<table>
		<tr>
			<th><?php echo 'Postings from ' . $endpoint->name; ?></th>
			<th>Status</th>
			<th>Publications Available</th>
		</tr>
	<?php if (isset($this->results) && count($this->results) > 0): ?>
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
						<div id="ajaxDiv_<?php echo $item['safe_guid']; ?>">
						<a href="#" class="nzb button" style="white-space:nowrap;"
							data-name="<?php echo htmlentities($seriesName); ?>"
							data-issue="<?php echo $issue; ?>"
							data-year="<?php echo $year; ?>"
							data-endpoint_id="<?php echo $this->endpoint_id; ?>"
							data-guid="<?php echo $item['guid']; ?>"
							data-url="<?php echo $item['url']; ?>"
							data-postedDate="<?php echo $item['publishedDate']; ?>"
							data-safe_guid="<?php echo $item['safe_guid']; ?>"
							>Download</a>
						</div>
					<?php else: ?>
					<div>
						<div>
							<span class="icon <?php echo ($flux->isSourceComplete()?'true':'false'); ?>"></span>
							<span class="break-word"><?php echo $flux->src_status ; ?></span>
						</div>
						<div>
							<span style="display:block" class="icon <?php echo ($flux->isFlux_error()?'false':'true'); ?>"></span>
							<span class="break-word"><?php echo $flux->dest_status ; ?></span>
						</div>
					</div>
					<?php endif; ?>
			</td>
			<td class="name">
				<?php if (is_array($publications) && count($publications) > 0 ) : ?>
					<?php foreach( $publications as $pub ) : ?>
						<h4><nobr><?php echo $pub->seriesName() . " - " . $pub->issue_num . " - " . $pub->publishedMonthYear(); ?></nobr></h4>
						<?php $mediaList = $pub->media(); if ( is_array($mediaList) && count($mediaList)  ) :?>
							<?php foreach( $mediaList as $idx => $media ) : ?>
								<P><?php echo $media->mediaType()->code . " - " . $media->formattedSize(); ?></p>
							<?php endforeach; ?>
						<?php else : ?>
							<em>no media available</em>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<em>No data for series named <span style="color:red"><?php echo $seriesName; ?></span></em>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php else : ?>
		<tr>
			<td colspan=3>No matching records</td>
		</tr>
	<?php endif; ?>
	</table>
</div>
<?php else : ?>
	<em><?php echo Localized::GlobalLabel("No Search Criteria"); ?></em>
<?php endif; ?>
  			</div>
		</div>
	</div>
</section>
