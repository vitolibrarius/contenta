<?php if (is_array($this->listArray) && count($this->listArray) > 0): ?>
<?php
	$seriesGroups = array();
	foreach($this->listArray as $publication) {
		$series_id = $publication->seriesName();
		$seriesGroups[$series_id][] = $publication;
	}
	ksort($seriesGroups);
?>

<?php foreach($seriesGroups as $series_id => $publicationArray): ?>
	<h3><?php echo $series_id; ?></h3>
	<div class="row">
		<?php foreach($publicationArray as $key => $publication): ?>
		<div class="grid_4">
		<figure class="card">
			<div class="figure_top">
				<div class="figure_image">
					<a href="#">
						<img src="<?php	echo Config::Web("Image", "thumbnail", "publication", $publication->id); ?>" class="thumbnail publication">
					</a>
				</div>
				<div class="figure_details">
					<div class="figure_detail_top">
						<?php if ($publication->publisher() != null): ?>
							<img src="<?php	echo Config::Web("Image", "icon", "publisher", $publication->publisher()->id); ?>" class="icon publisher">
						<?php endif; ?>
					</div>
					<div class="figure_detail_middle">
						<p class="pub_name"><?php echo $publication->name; ?></p>
						<p class="issue_num"><?php echo $publication->issue_num; ?></p>
						<p class="pub_date"><?php echo $publication->publishedMonthYear(); ?></p>
					</div>
				</div>
			</div>
			<figcaption class="caption">
				<p style="height:1em;"><span class="search_string" style="float:left"><?php echo $publication->searchString(); ?></span>
					<span class="search_date" style="float:right;"><?php echo $publication->formattedDate_search_date(); ?></span></p>
				<?php $rssMatch = $publication->rssMatches(); if ( is_array($rssMatch) && count($rssMatch) > 0 ) :?>
				<div class="mediaData rss flux">
					<table>
						<tr>
							<th>RSS Item</th>
							<th>Status</th>
						</tr>
				<?php foreach($rssMatch as $rss): ?>
						<tr>
							<td>
								<h4><?php echo $rss->displayName(); ?></h4>
								<span><?php echo date("M d, Y", $rss->pub_date); ?></span>
								<span><?php echo formatSizeUnits($rss->enclosure_length); ?></span>
								<p><?php echo $rss->endpoint()->name; ?></p>
								<?php echo ($rss->enclosure_password == true ? "<em>**** password protected</em>" : ""); ?>
							</td>
							<td>
					<?php $flux = $rss->flux(); if ($flux == false ) : ?>
						<?php if ($rss->endpoint() != false && $rss->endpoint()->isOverMaximum('daily_dnld_max') == false) : ?>
							<div id="dnld_<?php echo $rss->safe_guid(); ?>">
							<a href="#" class="nzb button" style="white-space:nowrap;"
								data-name="<?php echo htmlentities($rss->clean_name); ?>"
								data-issue="<?php echo $rss->clean_issue; ?>"
								data-year="<?php echo $rss->clean_year; ?>"
								data-endpoint_id="<?php echo $rss->endpoint_id; ?>"
								data-guid="<?php echo $rss->guid; ?>"
								data-url="<?php echo $rss->enclosure_url; ?>"
								data-postedDate="<?php echo $rss->pub_date; ?>"
								data-ref_guid="dnld_<?php echo $rss->safe_guid(); ?>"
								>Download</a>
							</div>
						<?php else: ?>
							<div style="white-space: nowrap;">
								<span class="icon false"></span>
								<span class="break-word"><?php echo ($rss->endpoint() ? "Over Daily Maximum" : "No Endpoint"); ?></span>
							</div>
							<div style="white-space: nowrap;">
								<span class="icon false"></span>
								<span class="break-word"><?php echo ($rss->endpoint() ? $rss->endpoint()->dailyMaximumStatus() : "No Status"); ?></span>
							</div>
						<?php endif; ?>
					<?php else: ?>
						<div>
							<div style="white-space: nowrap;">
								<span class="icon <?php echo ($flux->isSourceComplete()?'true':'false'); ?>"></span>
								<span class="break-word"><?php echo $flux->src_status ; ?></span>
							</div>
							<div style="white-space: nowrap;">
								<span class="icon <?php echo ($flux->isFlux_error()?'false':'true'); ?>"></span>
								<span class="break-word"><?php echo $flux->dest_status ; ?></span>
							</div>
						</div>
					<?php endif; ?>
							</td>
						</tr>
				<?php endforeach; ?>
					</table>
				</div>
				<?php endif; ?>
				<a href="#" class="srch button" style="white-space:nowrap;" data-pub_id="<?php echo $publication->id; ?>">Search now</a>
				<div id="ajaxDiv_<?php echo $publication->id; ?>"></div>
			</figcaption>
		</figure>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
<?php else: ?>
	No matching records
<?php endif; ?>
