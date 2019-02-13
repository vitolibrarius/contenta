<?php use html\Element as H ?>

<section>
	<div class="row">
<?php use \html\Paginator as Paginator;
	if ( isset($this->params) ) {
		$p = new Paginator( $this->params, Config::Web('/DisplayBook/searchBooks') );
		echo $p->render();
	}
?>
	</div>

	<div class="row">
<?php if (empty($this->listArray)): ?>
	<div style="background:hsl(326,50%,75%)">
		There are no matching records
	</div>
<?php else: ?>
	<table>
<?php foreach($this->listArray as $key => $value): ?>
	<tr style="vertical-align:top;">
		<td>
			<img src="<?php echo Config::Web( "Image", "thumbnail", "book", $value->id) ?>" class="thumbnail">
		</td>
		<td><h3><?php echo $value->name; ?> - <?php echo $value->author; ?></h3><hr>
			<?php echo $value->shortDescription(); ?>
			<a class="button" href="<?php echo Config::Web("/Api/bookPayload/" . $value->id);?>">
				Download  <b><?php echo $value->formattedSize(); ?></b>
			</a>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
<?php endif; ?>
	</div>
</section>
