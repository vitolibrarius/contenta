<h1 class="group">results</h1><section>
	<div class="row">
	<?php if (is_array($this->results) && count($this->results) > 0): ?>
		<?php foreach ($this->results as $key => $record): ?>
		<div class="grid_3">
		<figure class="card">
			<div class="figure_top <?php if ( $running == true ) { echo 'blocked'; } ?>" id="fig_<?php echo $key; ?>">
				<div class="figure_image">
				</div>
				<div class="figure_details">
					<div class="figure_detail_top">
					</div>
					<div class="figure_detail_middle">
						<p class="status">
							<?php echo Localized::Get("Upload", (isset($value, $value['status']) ? $value['status'] : 'unknown')); ?>
						</p>
						<p class="file_size">
							<?php echo (isset($value, $value['size']) ? formatSizeUnits($value['size']) : 'Unknown'); ?>
						</p>
					</div>
					<div class="figure_detail_bottom">
					</div>
				</div>
			</div>

			<figcaption class="caption">
				<pre><?php echo json_encode($record, JSON_PRETTY_PRINT); ?></pre>
			</figcaption>
		</figure>
		</div>
		<?php endforeach; ?>
	<?php else: ?>
		No matches
	<?php endif; ?>
	</div>
</section>
