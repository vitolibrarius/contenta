<div class="mediaData">
<table>
	<tr>
		<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
		<th><?php echo Localized::ModelLabel($this->model->tableName(), "issue" ); ?></th>
		<th><?php echo Localized::ModelLabel($this->model->tableName(), "year" ); ?></th>
	</tr>
<?php if (is_array($this->listArray) && count($this->listArray) > 0) : ?>
<?php foreach( $this->listArray as $item ) : ?>
	<tr>
		<td><?php echo $item->name(); ?></td>
		<td><?php echo $item->issue(); ?></td>
		<td><?php echo $item->year(); ?></td>
	</tr>
<?php endforeach; ?>
<?php else : ?>
	<tr>
		<td colspan="3">No data</td>
	</tr>
<?php endif; // has list ?>
</table>
</div>
