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
<?php if ( isset($endpoint, $endpoint->name) ) : ?>
<div class="row">
	<div class="grid_12">
	<h2><?php echo 'Postings from ' . $endpoint->name; ?></h2>
	</div>
</div>
<?php if (isset($this->results) && count($this->results) > 0): ?>
<?php foreach ($this->results as $key => $item) : ?>
<?php if (isset($item['metadata'], $item['metadata']['name'])) {
		$seriesName = $item['metadata']['name'];
		$issue = (isset($item['metadata']['issue']) ? $item['metadata']['issue'] : null);
		$year = (isset($item['metadata']['year']) ? $item['metadata']['year'] : null);
		$publications = Model::Named("Publication")->publicationsLike($seriesName, $issue, $year);
	}
?>
<div class="row"  style="background-color: #E3E3E3; margin: .8em;">
	<div class="grid_10">
				<h4><?php echo $item['title']; ?></h4>
				<p><?php echo date("M d, Y", $item['publishedDate']); ?></p>
				<p><?php echo formatSizeUnits($item['len']); ?></p>
				<?php echo ($item['password'] == true ? "<em>**** password protected</em>" : ""); ?>
	</div>
	<div class="grid_2"><!-- status -->
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
						<?php if ($flux->isSourceComplete() == false): ?>
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
	</div>
</div>
<div class="row">
	<?php if (is_array($publications) && count($publications) > 0 ) : ?>
	<?php foreach( $publications as $pub ) : ?>
		<div class="grid_2">
			<div style="box-shadow: 2px 2px 4px #aaa; border:1px solid #ccc;">
				<h4><?php echo $pub->seriesName() . " - " . $pub->issue_num . " - " . $pub->publishedMonthYear(); ?></h4>
				<?php $mediaList = $pub->media(); if ( is_array($mediaList) && count($mediaList)  ) :?>
					<?php foreach( $mediaList as $idx => $media ) : ?>
						<P><?php echo $media->mediaType()->code . " - " . $media->formattedSize(); ?></p>
					<?php endforeach; ?>
				<?php else : ?>
					<em>no media available</em>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
	<?php else: ?>
		<div class="grid_12">
					<em>No data for series named <span style="color:red">
						<?php echo $seriesName . " - " . $issue . " - " . $year ; ?></span></em>
		</div>
	<?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php else : ?>
<div class="row">
	<div class="grid_12">
	<em><?php echo Localized::GlobalLabel("No Search Criteria"); ?></em>
	</div>
</div>
<?php endif; ?>

</div> <!-- wrapper -->
</section>
