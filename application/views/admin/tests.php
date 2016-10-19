<style>
em {
	color: blue;
	font-weight:bold;
}
</style>
<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminTest/index'); ?>">Test Home</a></li>
		<li><a href="<?php echo Config::Web('/AdminTest/index') . "?test=filenames"; ?>">Filenames</a></li>
		<li><a href="<?php echo Config::Web('/AdminTest/index') . "?test=flagged"; ?>">Flagged Names</a></li>
	</ul>
</div>

<section>

<?php if (is_array($this->listArray) && count($this->listArray) > 0) : ?>
<?php foreach( $this->listArray as $idxHash => $item ) : ?>
<?php $source = (isset($item["source"]) ? $item["source"] : $idxHash);
	$url = rawurlencode($idxHash);
?>
<div class="row"  style="background-color: #E3E3E3; margin: .8em;">
	<div class="grid_10">
		<h4><?php echo $source; ?></h4>
<?php if (is_array($item) && count($item) > 0) : ?>
		<table>
<?php foreach( $item as $subidx => $subitem ) : ?>
	<tr>
		<td><?php echo $subidx; ?></td>
		<td><?php echo $subitem; ?></td>
	</tr>
<?php endforeach; ?>
		</table>
<?php else: ?>
	<em> no data </em>
<?php endif; ?>

	</div>
	<div class="grid_2">
		<a class="button" href='<?php echo Config::Web('/AdminTest/acceptFilenames') . "?hash=".$url."&test=".$this->testName; ?>'>Update</a>
		<a class="button" href='<?php echo Config::Web('/AdminTest/flagFilenames') . "?hash=".$url."&test=".$this->testName; ?>'>Flag</a>
		<a class="button" href='<?php echo Config::Web('/AdminTest/deleteFilenames') . "?hash=".$url."&test=".$this->testName; ?>'>Delete</a>
	</div>
</div>

<?php endforeach; ?>
<?php else: ?>
	<em> no test </em>
<?php endif; ?>
</section>
