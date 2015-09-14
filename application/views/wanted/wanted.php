<section>

<?php if (is_array($this->listArray) && count($this->listArray) > 0): ?>
<?php
	$seriesGroups = array();
	foreach($this->listArray as $publication) {
		$series_id = $publication->series_id;
		if (is_null($series_id)) {
			$series_id = 0;
		}

		$seriesGroups[$series_id][] = $publication;
	}
?>

<?php foreach($seriesGroups as $series_id => $publicationArray): ?>
	<div class="row">
		<div class="grid_12">
			<h2>
				<?php $groupName = "Unknown";
					if ($series_id > 0) {
						$seriesObj = Model::Named('Series')->objectForId($series_id);
						if ($seriesObj != false) {
							$groupName = $seriesObj->name;
						}
					}
					echo $groupName;
				?>
			</h2>
		</div>
	</div>

	<div class="row">
		<?php foreach($publicationArray as $key => $publication): ?>
		<div class="grid_4">
		<figure class="card">
			<div class="figure_top">
				<div class="figure_image">
					<a href="#">
						<img src="<?php
					echo Config::Web("Image", "thumbnail", "publication", $publication->id); ?>" class="thumbnail publication">
					</a>
				</div>
				<div class="figure_details">
					<div class="figure_detail_top">
						<?php
					if ($publication->publisher() != null): ?>
							<img src="<?php
						echo Config::Web("Image", "icon", "publisher", $publication->publisher()->id); ?>"
								class="icon publisher">
						<?php
					endif; ?>
					</div>
					<div class="figure_detail_middle">
						<p class="series_id"><?php
					echo $publication->seriesName(); ?></p>
						<p class="pub_name"><?php
					echo $publication->name; ?></p>
						<p class="issue_num"><?php
					echo $publication->issue_num; ?></p>
						<p class="pub_date"><?php
					echo $publication->publishedMonthYear(); ?></p>
					</div>
				</div>
			</div>
			<figcaption class="caption">
				<a href="#" class="srch button" style="white-space:nowrap;" data-pub_id="<?php echo $publication->id; ?>">Search now</a>
				<div id="ajaxDiv_<?php
					echo $publication->id; ?>"></div>
			</figcaption>
		</figure>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
<?php else: ?>
	No matching records
<?php endif; ?>

</section>
