<div class="mediaData">
	<table>
		<tr>
			<th></th>
			<th>Issue</th>
			<th>Published</th>
			<th>Series / Issue Details</th>
			<th>Story Arcs</th>
			<th></th>
		</tr>
	<?php if ( is_array($this->listArray) && count($this->listArray) > 0) : ?>
	<?php foreach ($this->listArray as $key => $publication): ?>
		<tr>
			<td><img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $publication->id); ?>"
				class="thumbnail recordType" />
			</td>
			<td class="issue"><?php echo $publication->issue_num; ?></td>
			<td class="published"><?php echo $publication->publishedMonthYear(); ?></td>
			<td class="name">
				<h4><?php echo $publication->seriesName(); ?></h4>
				<h5><?php echo $publication->name; ?></h5>
				<span class="description"><?php echo $publication->desc; ?></span>
			</td>
			<td>
				<ul class="badge story_arc">
				<?php foreach ($publication->story_arcs() as $key => $story_arc): ?>
					<li class="story_arc <?php echo ($story_arc->isWanted() ? 'high' : 'low'); ?>">
						<nobr><?php echo $story_arc->name; ?></nobr>
					</li>
				<?php endforeach; ?>
				</ul>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php else : ?>
		<tr>
			<td colspan=6>No matching records</td>
		</tr>
	<?php endif; ?>
	</table>
</div>
