<style type="text/css">
.row.data {
	background-color: #e2e2e2;
	vertical-align: middle;
	border-bottom: 1px solid #FFFFFF;
}
</style>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index'); ?>">Wanted</a></li>
	</ul>
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/index_story_arc'); ?>">Wanted Story Arcs</a></li>
	</ul>
	<ul>
		<li><a href="<?php echo Config::Web('/AdminWanted/newznab'); ?>">Manual Search</a></li>
	</ul>
</div><?php
	$adminPage = "Admin" . $this->model->modelName() . "/edit" . $this->model->modelName();
	$keys = array( "series/name", "name", "lastXupdated", "formattedDate_search_date", "publishedMonthYear", "issue_num"  );
	$activeKp = "series/isPub_active";
?>
<hr>
<h1>Total :<?php echo $this->total; ?></h1>
<hr>
<div class="mediaData">
	<?php if ($this->results): ?>
		<table>
			<tr>
				<th></th>
				<?php foreach ( $keys as $k ) : ?>
					<th><?php echo Localized::ModelLabel($this->model->tableName(), $k ); ?></th>
				<?php endforeach; ?>
				<?php if ( $activeKp != false ) : ?>
					<th><?php echo Localized::ModelLabel($this->model->tableName(), $activeKp ); ?></th>
				<?php endif; ?>
				<th></th>
			</tr>
			<?php foreach($this->results as $idx => $value): ?>
				<tr>
					<td>
						<a href="<?php echo Config::Web($adminPage, $value->pkValue()); ?>">
							<img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $value->pkValue()); ?>" class="thumbnail">
						</a>
					</td>
					<?php foreach ( $keys as $k ) : ?>
						<td class="<?php echo sanitize_html_id($k); ?>"><?php echo $value->{$k}(); ?></td>
					<?php endforeach; ?>
					<?php if ( $activeKp != false ) : ?>
						<td class="active"><span class="icon <?php echo ($value->{$activeKp}() ? 'true' : 'false'); ?>"></span></td>
					<?php endif; ?>
					<td>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p>No records pending</p>
	<?php endif; ?>
</div>
