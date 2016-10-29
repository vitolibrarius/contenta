<style type="text/css">
	.grid_xx {background-color:blue; word-wrap:break-word;}
</style>

<section>
	<div class="row"><div class="grid_12">
<?php use \html\Paginator as Paginator;
	$p = new Paginator( $this->params, Config::Web('/AdminMedia/searchMedia') );
	echo $p->render();
?>
	</div></div>

	<div class="row">

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
	<h3 class="group"><?php echo $series_id; ?></h3>
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
						<p class="series_id"><?php echo $publication->seriesName(); ?></p>
						<p class="pub_name"><?php echo $publication->name; ?></p>
						<p class="issue_num"><?php echo $publication->issue_num; ?></p>
						<p class="pub_date"><?php echo $publication->publishedMonthYear(); ?></p>
					</div>
				</div>
			</div>
			<figcaption class="caption">
				<div class="mediaData">
					<table>
				<?php foreach( $publication->media() as $media ) : ?>
					<tr>
					<td>
						<img src="<?php echo Config::Web('/AdminMedia/iconForMedia', $media->id) ?>" class="thumbnail">
					</td>
					<td>
						<div id="media_<?php echo $media->id; ?>">
							<p><?php echo $media->formattedSize() ?></p>
							<a href="<?php echo Config::Web('/AdminMedia/reprocessMedia', $media->id); ?>" class="button" alt="Reprocess">
								<?php echo $this->label( "index", "ReprocessButtonText"); ?>
							</a>
							<a href="#" class="confirm button"
								data_action="<?php echo Config::Web('/AdminMedia/deleteMedia', $media->id); ?>"
								data_key="<?php echo $media->id; ?>"
								data_filename="<?php echo (isset($media->filename) ? $media->filename : $media->id); ?>"
								alt="Delete">
									<?php echo $this->label( "index", "DeleteButtonText"); ?>
							</a>
						</div>
					</td>
					</tr>
				<?php endforeach; ?>
					</table>
				</div>
			</figcaption>
		</figure>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>

<?php else: ?>
		<div style="background:hsl(326,50%,75%)">
			There are no matching records
		</div>
<?php endif; ?>

	</div>
</section>
