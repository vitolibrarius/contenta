<?php
	$keys = array();
	$activeKp = false;
	switch( $this->model->tableName() ) {
		case 'series':
			$keys = array( "name", "start_year", "lastXupdated", "lastPublication/publishedMonthYear", "lastPublication/issue_num" );
			$activeKp = "isPub_active";
			break;
		case 'story_arc':
			$keys = array( "name", "lastXupdated", "lastPublication/publishedMonthYear", "lastPublication/issue_num" );
			$activeKp = "isPub_active";
			break;
		case 'publisher':
			$keys = array( "name", "lastXupdated" );
			break;
		case 'character':
			$keys = array( "name", "lastXupdated" );
			break;
		case 'publication':
			$keys = array( "series/name", "name", "lastXupdated", "publishedMonthYear", "issue_num"  );
			$activeKp = "series/isPub_active";
			break;
		default: break;
	}
?>
<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/Admin/updatePending') . "?model=Publisher"; ?>">Publishers</a></li>
		<li><a href="<?php echo Config::Web('/Admin/updatePending') . "?model=Series"; ?>">Series</a></li>
		<li><a href="<?php echo Config::Web('/Admin/updatePending') . "?model=Story_Arc"; ?>">Story Arcs</a></li>
		<li><a href="<?php echo Config::Web('/Admin/updatePending') . "?model=Character"; ?>">Characters</a></li>
		<li><a href="<?php echo Config::Web('/Admin/updatePending') . "?model=Publication"; ?>">Publications</a></li>
	</ul>
</div>

<div class="mediaData">
	<?php if ($this->listArray): ?>
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
			<?php foreach($this->listArray as $idx => $value): ?>
				<tr>
					<td><img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $value->pkValue()); ?>" class="thumbnail"></td>
					<?php foreach ( $keys as $k ) : ?>
						<td class="<?php echo sanitize_html_id($k); ?>"><?php echo $value->{$k}(); ?></td>
					<?php endforeach; ?>
					<?php if ( $activeKp != false ) : ?>
						<td class="active"><span class="icon <?php echo ($value->{$activeKp}() ? 'true' : 'false'); ?>"></span></td>
					<?php endif; ?>
					<td>
						<a href="<?php echo Config::Web('/Admin/refreshObject', $this->model->tableName(), $value->pkValue()); ?>"
							class="button" style="white-space:nowrap;">Refresh now</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p>No records pending</p>
	<?php endif; ?>
</div>
