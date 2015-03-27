<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/Upload/index'); ?>"><?php echo $this->label( "UploadLink", "name" ); ?></a></li>

	<?php $min = 0; $max = $this->pageCount;
	if ($this->pageCurrent > 5) {
		$min = $this->pageCurrent - 5;
	}
	if ($max - $this->pageCurrent > 5 ) {
		$max = $this->pageCurrent + 5;
	}
	if ( $min > 0 ) {
		echo '<li><a href="'. Config::Web('/AdminUploadRepair/index/0') .'">First</a></li><li>....</li>';
	}
	for ($x = $min; $x < $max; $x++) {
		if ( $x == $this->pageCurrent ) {
			echo '<li class="current">';
		}
		else {
			echo '<li>';
		}
		echo '<a href="'. Config::Web('/AdminUploadRepair/index', $x) . '">' . $x . '</a></li>';
	}
	if ( $max < $this->pageCount -1 ) {
		echo '<li>....</li><li><a href="'. Config::Web('/AdminUploadRepair/index', ($this->pageCount -1)) . '">Last</a></li>';
	}
	?>
	</ul>
</div>

<div class="mediaData">
	<table>
		<tr>
			<th></th>
			<th>Type</th>
			<th>Name</th>
			<th>Size</th>
			<th></th>
		</tr>
	<?php if ($this->active): ?>
	<?php foreach ($this->active as $key => $value): ?>
		<tr>
			<td>
				<a href="<?php echo Config::Web('/AdminUploadRepair/editInProcess', $key); ?>">
				<img src="<?php echo Config::Web('/AdminUploadRepair/firstThumbnail', $key) ?>" class="thumbnail">
				</a>
			</td>
			<td><?php echo (isset($value, $value['filename']) ? file_ext($value['filename']) : 'Unknown'); ?></td>
			<td>
				<h3 class="path"><?php echo (isset($value, $value['name']) ? $value['name'] : 'No Source'); ?></h3>
				<a href="#" limit="5" name="<?php echo $key; ?>" class="ajaxLogs">Logs</a>
				<div id="ajaxDiv_<?php echo $key; ?>"/></td>
			</td>
			<td><?php echo (isset($value, $value['size']) ? formatSizeUnits($value['size']) : 'Unknown'); ?></td>
			<td>
				<div class="retry">
					<a class="retry_icon" href="<?php echo Config::Web('/AdminUploadRepair/reprocess', $key); ?>" alt="Retry"></a>
				</div>
				<div class="edit">
					<a class="edit_icon" href="<?php echo Config::Web('/AdminUploadRepair/editInProcess', $key); ?>" alt="Edit"></a>
				</div>
				<div class="trash"><a class="trash_icon" href="#openDeleteConfirm" alt="Delete"></a></div>

				<!-- div id="openDeleteConfirm" class="modalDialog">
					<div>
						<a href="#close" title="Close" class="close">X</a>
						<h2>Confirm Delete</h2>
						<div>
							<div style="float:left ; width:20%;"><img src="<?php echo WEB_DIR; ?>/AdminUploadRepair/firstThumbnail/<?php echo $key; ?>" class="icon"></div>
							<div style="float:right; width:75%;">
								This will permanently delete
									<b><?php echo (isset($value, $value['name']) ? $value['name'] : 'No Source'); ?></b>
								<br>
								<a class="btn" href="<?php echo WEB_DIR; ?>/AdminUploadRepair/deleteInProcess/<?php echo $key; ?>">Delete</a>
							</div>
						</div>
						<div style="clear:both;"></div>
					</div>
				</div -->
			</td>
		</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</table>
</div>

