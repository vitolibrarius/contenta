<div class="mediaData">
	<?php if ($this->listArray): ?>
		<table>
			<tr>
				<th></th>
				<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
				<th><?php echo Localized::ModelLabel($this->model->tableName(), "start_year" ); ?></th>
				<th><?php echo Localized::ModelLabel($this->model->tableName(), "xupdated" ); ?></th>
				<th><?php echo Localized::ModelLabel($this->model->tableName(), "lastPublicationDate" ); ?></th>
				<th><?php echo Localized::ModelLabel($this->model->tableName(), "lastPublicationIssue" ); ?></th>
				<th><?php echo Localized::ModelLabel($this->model->tableName(), "isActive" ); ?></th>
			</tr>
			<?php foreach($this->listArray as $key => $value): ?>
				<?php $lastPub = $value->lastPublication() ?>
				<tr>
					<td><img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $value->pkValue()); ?>" class="thumbnail"></td>
					<td><?php echo $value->name; ?></td>
					<td><?php echo $value->start_year; ?></td>
					<td><?php echo $value->lastXupdated(); ?></td>
					<td><?php echo $lastPub->publishedMonthYear(); ?></td>
					<td><?php echo $lastPub->issue_num; ?></td>
					<td><span class="icon <?php echo ($value->isActive() ? 'true' : 'false'); ?>"></span></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p>No records pending</p>
	<?php endif; ?>
</div>
