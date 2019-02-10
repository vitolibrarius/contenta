<table border="1">
	<tr>
		<th>key
		</th>
		<td>
	<a class="button" href="<?php echo Config::Web('/AdminUploadRepair/epub_accept/', $this->key); ?>">
		Import
	</a>
		</td>
	</tr>
	<?php foreach ($this->opf as $key => $item): ?>
	<tr>
		<td>
			<?php echo $key; ?>
		</td>
		<td>
			<?php echo $item; ?>
		</td>
	</tr>
	<?php endforeach; ?>

</table>
