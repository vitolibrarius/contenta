<?php
	$endpointType = null;
	$endpoint = null;
	if ( isset($this->endpoint_id) ) {
		$endpoint = Model::Named( 'Endpoint' )->objectForId($this->endpoint_id);
		if ( $endpoint ) {
			$endpointType = $endpoint->type();
		}
	}
?>
<div class="mediaData">
	<table>
		<tr>
			<th>Postings from <?php echo $endpoint->name; ?></th>
			<th>Status</th>
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
				<?php $flux = $this->fluxModel->objectForSourceIdEndpointGUID( $this->endpoint_id, $item['guid'] );
					if ($flux == false ) : ?>
						<div id="ajaxDiv_<?php echo $item['safe_guid']; ?>">
						<a href="#" class="nzb btn" style="white-space:nowrap;"
							data-name="<?php echo $item['title']; ?>"
							data-endpoint_id="<?php echo $this->endpoint_id; ?>"
							data-guid="<?php echo $item['guid']; ?>"
							data-url="<?php echo $item['url']; ?>"
							data-postedDate="<?php echo $item['publishedDate']; ?>"
							data-safe_guid="<?php echo $item['safe_guid']; ?>"
							>
							<img style="max-width: 20px; max-height: 20px;" src="<?php echo $endpointType->favicon(); ?>">&nbsp;Download
						</a>
						</div>
					<?php else: ?>
						<div>
							<div>
								<span class="icon <?php echo ($flux->isSourceComplete()?'true':'false'); ?>"></span>
								<?php echo $flux->src_status ; ?>
							</div>
							<div>
								<span style="display:block" class="icon <?php echo ($flux->isError()?'false':'true'); ?>"></span>
								<?php echo $flux->dest_status ; ?>
							</div>
						</div>
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
