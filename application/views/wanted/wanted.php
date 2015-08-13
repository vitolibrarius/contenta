<?php if ( is_array($this->listArray) && count($this->listArray) > 0) : ?>
<?php foreach ($this->listArray as $key => $publication): ?>
<figure class="card" style="width: 380px;">
	<div class="feature">
		<div class="feature_top">
			<div class="feature_top_left">
				<a href="#">
					<img src="<?php echo Config::Web( "Image", "thumbnail", "publication", $publication->id);?>" class="thumbnail publication">
				</a>
			</div>
			<div class="feature_top_right">
				<div class="feature_top_right_top">
					<?php if ($publication->publisher() != null) : ?>
						<img src="<?php echo Config::Web( "Image", "icon", "publisher", $publication->publisher()->id);?>"
							class="icon publisher">
					<?php endif; ?>
				</div>
				<div class="feature_top_right_middle">
					<span class="details">
						<span class="series_id"><?php echo $publication->seriesName(); ?></span>
						<span class="pub_name"><?php echo $publication->name; ?></span>
						<span class="issue_num"><?php echo $publication->issue_num; ?></span>
						<span class="pub_date"><?php echo $publication->publishedMonthYear(); ?></span>
					</span>
				</div>
				<div class="feature_top_right_bottom">
				<a href="#" class="srch btn" style="white-space:nowrap;"
					data-pub_id="<?php echo $publication->id; ?>">Search now</a>
				</div>
			</div>
		</div>
	</div>

	<div class="clear"></div>

	<figcaption class="caption">
		<div id="ajaxDiv_<?php echo $publication->id;?>"></div>
	</figcaption>
</figure>
<?php endforeach; ?>
<?php else : ?>
	No matching records
<?php endif; ?>
