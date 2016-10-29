<section>
	<div class="row"><div class="grid_12">
<?php use \html\Paginator as Paginator;
	$p = new Paginator( $this->params, Config::Web('/AdminPullList/searchPullLists') );
	echo $p->render();
?>
	</div></div>
</section>

<?php if (isset($this->listArray) && count($this->listArray) > 0) : ?>
<?php
	$groups = array();
	foreach($this->listArray as $item) {
		$group = $item->pull_list_group()->name;
		$groups[$group][] = $item;
	}
	ksort($groups);

?>
<div class="mediaData">
<?php foreach( $groups as $groupName => $groupList ) : ?>
<h2><?php echo $groupName; ?></h2>
<table>
	<tr>
		<th><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></th>
		<th><?php echo Localized::ModelLabel($this->model->tableName(), "issue" ); ?></th>
		<th><?php echo Localized::ModelLabel($this->model->tableName(), "year" ); ?></th>
		<th><?php echo Localized::ModelLabel("pull_list", "published" ); ?></th>
	</tr>
<?php foreach( $groupList as $item ) : ?>
	<tr>
		<td><?php echo $item->name(); ?></td>
		<td><?php echo $item->issue(); ?></td>
		<td><?php echo $item->year(); ?></td>
		<td><?php echo $item->pull_list()->formattedDate_published(); ?></td>
	</tr>
<?php endforeach; ?> <!-- items -->
</table>
<?php endforeach; ?> <!-- groups -->
</div>
<?php else : ?>
		<em>No data</em>
<?php endif; // has list ?>
