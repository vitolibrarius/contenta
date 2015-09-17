<?php if (isset($this->marquee)): ?>
<div id="marquee">
	<div class="photobanner">
		<?php foreach( $this->marquee as $idx => $src) {
			echo "<img ";
			if ($idx == 0) { echo "class='first'"; }
			echo "src='" . $src . "'/>";
		}
		?>
	</div>
</div>
<br />
<br />
<?php endif; ?>

<section>
	<div class="row">

<?php if (Session::get('user_logged_in') == true):?>

	<?php
		$card = new html\Card();
		$card->setThumbnailTable( "publication" );
		$card->setThumbnailPrimaryKeypath( "publication_id" );
		$card->setDetailKeys( array(
				model\Publication::issue_num => "publication/issue_num",
				model\Publication::pub_date => "publication/publishedMonthYear"
			)
		);
		$card->setDisplayNameKey( "publication/series/name" );
		$card->setDisplayDescriptionKey( "publication/shortDescription" );
		if ( is_null($this->recentMedia) || count($this->recentMedia) == 0) {
			echo "No records";
		}

		foreach($this->recentMedia as $key => $media) {
// 			if ( isset($this->editAction) ) {
// 				$card->setEditPath( $this->editAction . '/' . $value->id );
// 			}
// 			if ( isset($this->deleteAction) ) {
// 	 			$card->setDeletePath( $this->deleteAction . '/' . $value->id );
// 			}
			echo '<div class="grid_3">' . PHP_EOL;
			echo $card->render($media);
			echo '</div>' . PHP_EOL;
		}
	?>

<?php endif; ?>

	</div>
</section>
