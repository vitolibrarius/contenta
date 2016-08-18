<?php if (is_array($this->listArray) && count($this->listArray) > 0) : ?>
<?php foreach( $this->listArray as $idx => $rss ) : ?>
<?php if ( $idx % 6 == 0) : ?>
	<?php $open = true; ?>
	<div class="row">
<?php endif; // modulo ?>

	<div class="grid_2">
	<div class="<?php echo $this->model->tableName(); ?>">
		<h4><?php echo $rss->clean_name(); ?></h4>
		<div class="issue_year">
			<span class="issue large"><?php echo $rss->clean_issue(); ?></span>
			<span class="year"><?php echo $rss->clean_year(); ?></span>
		</div>

		<p class="details pub_date"><?php echo Localized::ModelLabel($this->model->tableName(), "pub_date" ); ?>:
			<span><?php echo date("M d, Y", $rss->pub_date); ?></span>
		</p>
		<p class="details enclosure_length"><?php echo Localized::ModelLabel($this->model->tableName(), "enclosure_length" ); ?>:
			<span><?php echo formatSizeUnits($rss->enclosure_length); ?></span>
		</p>
		<p class="details endpoint_id"><?php echo Localized::ModelLabel($this->model->tableName(), "endpoint_id" ); ?>:
			<span><?php echo $rss->endpoint()->name(); ?></span>
		</p>
		<?php echo ($rss->enclosure_password == true ? "<em>**** password protected</em>" : ""); ?>
		<?php $flux = $rss->flux(); if ($flux == false ) : ?>
			<div class="status flux" id="dnld_<?php echo $rss->safe_guid(); ?>">
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

<?php if ( ($idx +1) % 6 == 0 ) : ?>
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
