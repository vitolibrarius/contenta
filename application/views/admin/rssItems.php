<section>
	<div class="row"><div class="grid_12">
<?php use \html\Paginator as Paginator;
	$p = new Paginator( $this->params, Config::Web('/AdminPullList/searchRss') );
	echo $p->render();
	$comicVine = Model::Named("Endpoint")->ComicVineEndpoint();
?>
	</div></div>

<?php if (is_array($this->listArray) && count($this->listArray) > 0) : ?>
<?php foreach( $this->listArray as $idx => $rss ) : ?>
<?php if ( $idx % 4 == 0) : ?>
	<?php $open = true; ?>
	<div class="row">
<?php endif; // modulo ?>

	<div class="grid_3">
	<div class="<?php echo $this->model->tableName(); ?>">
		<div style="float: right;">
			<a target="comicvine" href="https://comicvine.gamespot.com/search/?q=<?php echo $rss->clean_name(); ?>&indices[]=volume"><img src="<?php echo $comicVine->endpointType()->favicon(); ?>"></a>
		</div>
		<h4><a href="#" class="togglable">
			<div class="toggle_item" style="display:none; font-size: 0.75em">
				<span class="nzbsearch title"><?php echo $rss->title; ?></span>
			</div>
			<div class="toggle_item">
				<span class="nzbsearch clean_name"><?php echo $rss->clean_name(); ?></span>
			</div>
		</a></h4>
		<div class="issue_year">
			<span class="issue large"><?php echo $rss->clean_issue(); ?></span>
			<span class="year"><?php echo $rss->clean_year(); ?></span>
		</div>

		<p class="details pub_date"><?php echo Localized::ModelLabel($this->model->tableName(), "pub_date" ); ?>:
			<span><?php echo date("M d, Y", $rss->pub_date); ?></span>
		</p>
		<p class="details enclosure_length"><?php echo Localized::ModelLabel($this->model->tableName(), "enclosure_length" ); ?>:
			<span><?php echo ($rss->enclosure_length > 0 ? formatSizeUnits($rss->enclosure_length) : Localized::GlobalLabel("Unknown")) ; ?></span>
		</p>
		<p class="details endpoint_id"><?php echo Localized::ModelLabel($this->model->tableName(), "endpoint_id" ); ?>:
			<span><?php echo $rss->endpoint()->name(); ?></span>
		</p>
		<?php echo ($rss->enclosure_password == true ? "<em>**** password protected</em>" : ""); ?>

		<?php $flux = $rss->flux(); if ($flux == false ) : ?>
			<?php $pubs = $rss->publicationMatches(); if (is_array($pubs) && count($pubs) > 0) : ?>
				<div class="mediaData">
					<table width="100%">
						<tr>
							<th></th>
							<th>Series</th>
							<th>Issue</th>
							<th>Year</th>
							<th>Media</th>
						</tr>
						<?php foreach( $pubs as $idx => $pub ) : ?>
						<tr>
							<td><img class="thumbnail cbz" src="<?php echo Config::Web('/Image/icon/series', $pub->series_id ) ?>"></td>
							<td><?php echo $pub->seriesName(); ?></td>
							<td><?php echo $pub->issue_num; ?></td>
							<td><?php echo $pub->publishedYear(); ?></td>
							<td><?php $media = $pub->media(); $last = array_last_value($media);
								foreach( $media as $m ) {
								echo $m->type_code . " " . $m->formattedSize();
								if ( $m != $last ) { echo "<hr>"; }
							} ?></td>
						</tr>
						<?php endforeach; ?>
					</table>
				</div>
			<?php endif; ?>
			<?php if ($rss->endpoint() != false && $rss->endpoint()->isOverMaximum('daily_dnld_max') == false) : ?>
				<div class="status" id="dnld_<?php echo $rss->safe_guid(); ?>">
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
			<div class="status flux">
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
	</div>
	</div>

<?php if ( ($idx +1) % 4 == 0 ) : ?>
	<?php $open = false; ?>
	</div>
<?php endif; // modulo ?>

<?php endforeach; ?>
<?php else : ?>
<div class="row">
	<div class="grid_3">
		<span>No data</span>
	</div>
</div>
<?php endif; // has list ?>

<?php if (isset($open) && $open == true) : ?>
	</div>
<?php endif; // open ?>
</section>
