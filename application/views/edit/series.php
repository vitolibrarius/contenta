<?php use html\Element as H ?>

<section>
	<div class="row">
		<div class="grid_4">

		<form method="post" accept-charset="utf-8"
			action="<?php echo Config::Web($this->saveAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
			name="editForm">

			<fieldset>
			<legend><?php echo Localized::ModelLabel($this->model->tableName(),"FormLegend"); ?></legend>

			<?php
				$realObj = (isset($this->object)) ? $this->object : null;
				$realType = null;
			?>

			<?php foreach ($this->model->attributesFor($realObj, $realType) as $attr => $form_type) : ?>

				<div class="">
				<?php $attrEditable = $this->model->attributeIsEditable($realObj, $realType, $attr);
					$attrName = $this->model->attributeName($realObj, $realType, $attr);
					$attValue = null;
					if ( isset($_POST, $_POST[$attrName]) ) {
						$attValue = $_POST[$attrName];
					}
					else if (isset($this->{$attr})) {
						$attValue = $this->{$attr};
					}

					$this->renderFormField( $form_type, $realObj, $realType, $this->model, $attr, $attValue, $attrEditable );
				?>
				</div>
			<?php endforeach; ?>

			<div class="half">
				<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("SaveButton"); ?>" />
			</div>

			<div class="half omega">
				<input type="reset"value="<?php echo Localized::GlobalLabel("ResetButton"); ?>"/>
			</div>
			</fieldset>
		</form>

		</div>

		<?php if (isset($this->object, $this->additionalAction) && null != $this->object->externalEndpoint()) : ?>
			<div class="grid_3">

			<form method="post"
				action="<?php echo Config::Web($this->additionalAction); ?>/<?php echo (isset($this->object)) ? $this->object->id : null; ?>"
				name="editForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"AdditionalLegend"); ?></legend>

					<?php $endpointType = $this->object->externalEndpoint()->endpointType(); ?>
					<label>Metadata Source</label>
					<div>
						<span class="input_restriction">
							<a href="<?php echo $this->object->xurl; ?>" target="<?php echo $endpointType->name; ?>">
								<?php echo $endpointType->name; ?>
								<img style="display:inline; float:right; max-width: 20px; max-height: 20px;"
									src="<?php echo $endpointType->favicon(); ?>">
							</a>
						</span>
					</div>

					<label><?php echo Localized::ModelLabel($this->model->tableName(), "xupdated"); ?></label>
					<input class="xupdated" type="text" name="xupdated" disabled
						value="<?php echo $this->object->formattedDate('xupdated'); ?>"
					/>

					<div class="half">
						<label><?php echo Localized::ModelLabel($this->model->tableName(), Model::IconName); ?></label>
						<img src="<?php echo Config::Web( "Image", "icon", $this->model->tableName(), $this->object->id); ?>" />
					</div>

					<div class="half omega">
						<label><?php echo Localized::ModelLabel($this->model->tableName(), Model::ThumbnailName); ?></label>
						<img src="<?php echo Config::Web( "Image", "thumbnail", $this->model->tableName(), $this->object->id); ?>" />
					</div>

				<div>
					<input type="submit" name="refreshEndpoint" value="<?php echo Localized::GlobalLabel("RefreshFromEndpoint"); ?>" />
				</div>
				</fieldset>
			</form>

			</div>
		<?php endif; ?>

		<?php if (isset($this->object)) : ?>
			<div class="grid_3">
				<div class="badges">
				<?php $list = $this->object->series_artists();
					if ( is_array($list) && count($list) > 0 ): ?>
					<?php
						$roleGroups = array();
						foreach($list as $serArtist) {
							$key = $serArtist->roleName();
							$artistGroups[$key][] = $serArtist->artist();
						}
						ksort($artistGroups); ?>
					<?php foreach ($artistGroups as $role => $artists): ?>
					<div class="<?php echo $role; ?>">
						<h6><?php echo $role; ?></h6>
						<ul class="badge artist">
						<?php foreach ($artists as $artist): ?>
							<li class="artist">
								<?php if ( isset($this->artistAction) ): ?>
									<a href="<?php echo Config::Web($this->artistAction, $artist->id); ?>">
								<?php endif; ?>
								<?php echo $artist->name(); ?>
								<?php if ( isset($this->artistAction) ) :?></a><?php endif; ?>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php $list = $this->object->characters();
					if ( is_array($list) && count($list) > 0 ): ?>
					<h6>Characters</h6>
					<ul class="badge characters">
					<?php foreach ($list as $character): ?>
						<li class="character">
							<?php if ( isset($this->editCharacterAction) ): ?>
								<a href="<?php echo Config::Web($this->editCharacterAction, $character->id); ?>">
							<?php endif; ?>
							<?php echo $character->name; ?>
							<?php if ( isset($this->editCharacterAction) ) :?></a><?php endif; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php $list = $this->object->story_arcs();
					if ( is_array($list) && count($list) > 0 ): ?>
					<h6>Story Arcs</h6>
					<ul class="badge story_arc">
					<?php foreach ($list as $story): ?>
						<li class="story_arc">
							<?php if ( isset($this->editStoryArcAction) ): ?>
								<a href="<?php echo Config::Web($this->editStoryArcAction, $story->id); ?>">
							<?php endif; ?>
							<?php echo $story->name; ?>
							<?php if ( isset($this->editStoryArcAction) ) :?></a><?php endif; ?>
						</li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>

<?php if (isset($this->object)) : ?>
<section>
	<div class="row">
		<?php if (empty($this->object->publications())): ?>
			<div style="background:hsl(326,50%,75%)">
				There are no publications
			</div>
		<?php else: ?>
			<?php
				$card = new html\Card();
				$card->setDisplayDescriptionKey( "shortDescription" );
				$card->setDetailKeys( array(
					\model\media\Publication::issue_num => "issue_num",
					\model\media\Publication::pub_date => "publishedMonthYear",
					)
				);

				foreach($this->object->publications() as $key => $value) {
					if ( isset($this->editPublicationAction) ) {
						$card->setEditPath( $this->editPublicationAction . '/' . $value->id );
					}
					if ( isset($this->deletePublicationAction) ) {
						$card->setDeletePath( $this->deletePublicationAction . '/' . $value->id );
					}
					echo '<div class="grid_3">' . PHP_EOL;
					echo $card->render($value, function() use($value) {
							$all_media = $value->media();
							if ( is_array($all_media) ) {
								foreach ($all_media as $idx => $media) {
									$c[] = H::p( $media->formattedSize(),
										H::a( array( "href" => Config::Web("/Api/mediaPayload/" . $media->id)),
												H::img( array( "src" => Config::Web("/public/img/download.png" )))
											),
										H::a( array( "target" => "slideshow",
											"href" => Config::Web("/DisplayMedia/slideshow/".$media->id)),
											H::img( array( "src" => Config::Web("/public/img/slideshow.png") ))
											)
									);
								}
							}
							return (isset($c) ? $c : null);
						}
					);
					echo '</div>' . PHP_EOL;
				}
			?>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>
