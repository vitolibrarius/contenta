<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/netconfig/index'); ?>"><span class="">Endpoints</span></a></li>
	</ul>
</div>

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
					$realType = (isset($this->endpoint_type) ? $this->endpoint_type : null);
					if ( is_null($realObj) == false && is_null($realObj->endpointType()) == false ) {
						$realType = $realObj->endpointType();
					}
				?>
					<label>Type</label>
					<div>
						<span class="input_restriction">
							<a href="<?php echo $realType->site_url; ?>" target="<?php echo $realType->name; ?>">
								<img style="display:inline; float:right; max-width: 20px; max-height: 20px;"
									src="<?php echo $realType->favicon(); ?>">
							</a>
							<?php echo $realType->comments; ?>
						</span>
					</div>
					<input class="type" type="text" name="displayType" disabled
						value="<?php echo $realType->displayName(); ?>"
					/>
					<input class="type" type="hidden" name="endpoint-type_code"
						value="<?php echo $realType->code; ?>"
					/>

				<?php foreach ($this->model->attributesFor($realObj, $realType) as $attr => $form_type) {
						$attrEditable = $this->model->attributeIsEditable($realObj, $realType, $attr);
						$attrName = $this->model->attributeName($realObj, $realType, $attr);
						$attValue = null;
						if ( isset($_POST, $_POST[$attrName]) ) {
							$attValue = $_POST[$attrName];
						}
						else if (isset($this->{$attr})) {
							$attValue = $this->{$attr};
						}

						$this->renderFormField( $form_type, $realObj, $realType, $this->model, $attr, $attValue, $attrEditable );
					}
				?>
				<div class="half">
					<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("SaveButton"); ?>" />
				</div>

				<div class="half omega">
					<input type="reset"value="<?php echo Localized::GlobalLabel("ResetButton"); ?>"/>
				</div>
				<br>
				</fieldset>
			</form>

			</div>

			<?php if ( isset($this->object)): ?>
			<div class="grid_8">
				<div>
					<a href="#" class="test button" style="white-space:nowrap;"
						data-href="<?php echo Config::Web($this->testAction, $this->object->id); ?>"
						>
						Test Connection
					</a>
					<pre id="ajaxDiv"></pre>
				</div>
				<div>
					<a href="<?php echo Config::Web($this->clearErrorsAction, $this->object->id); ?>" class="button" style="white-space:nowrap;">
						Reset Error Count
					</a>
				</div>

				<div class="mediaData">
				<h3>Jobs</h3>
				<?php $jobs = $this->object->jobs(); if ( is_array($jobs) && count($jobs) > 0) : ?>
					<table width="100%">
						<tr><th></th><th>Type</th><th>Failed</th><th>Next</th><th>Actions</th></tr>
					<?php
					foreach( $jobs as $idx => $j ) {
						$runningJobs = \Model::Named("Job_Running")->allForJob($j);
						$running = ( is_array($runningJobs) && count($runningJobs) > 0 );
						$endpointRequired = boolval($j->{"jobType/isRequires_endpoint"}());
						$endpointEnabled = boolval($j->{"endpoint/isEnabled"}());

						echo '<tr class="'. ( $running == true ? "blocked": "") . '">'
						. '<td><span class="icon ' . ($j->isEnabled() ? 'true' : 'false') . '"></span></td>'
						. '<td><p>' . $j->{"jobType/name"}() . '</p></td>'
						. '<td><p>' . ($j->last_fail ? $j->formattedDateTime_last_fail() : "") . '</p></td>'
						. '<td><p>' . $j->nextDate() . '</p></td>'
						. '<td>';

						echo '<a style="padding: 1em;" href="' . Config::Web('/AdminJobs/edit/', $j->id) . '"><span class="icon edit" /></a>';
						if ( $j->enabled && ($endpointRequired == false || $endpointEnabled) ) {
							echo '<a style="padding: 1em;" href="' . Config::Web('/AdminJobs/execute/'. $j->id) . '"><span class="icon run" /></a>';
						}
						echo '<a style="padding: 1em;" class="confirm" action="' . Config::Web('/AdminJobs/delete/', $j->id) . '" href="#"><span class="icon recycle"></span></a>';

						echo '</td></tr>';
					} ?>
					</table>
				<?php else : ?>
					<em>No scheduled jobs</em>
				<?php endif; ?>

				<?php if ( $this->object->endpointType() && $this->object->endpointType()->isRSS() ) : ?>
				<div>
					<h3>RSS Activity</h3>
					<?php $activity = $this->object->rssCount();
					foreach( $activity as $age => $count ) {
						echo '<p>' . $count . ' in the last ' . formattedTimeElapsed($age) . '</p>';
					} ?>
				</div>
				<?php endif; ?>

				<?php if ( $this->object->endpointType() && $this->object->endpointType()->isSABnzbd() ) : ?>
				<div>
					<h3>SabNZBD Activity</h3>
					<?php $activity = $this->object->fluxDestCount();
					foreach( $activity as $age => $counts ) {
						if ( is_array( $counts ) && count($counts) > 0 ) {
							echo '<p>';
							foreach ( $counts as $idx => $status ) {
								$count = $status->count;
								$dest_status = $status->dest_status;
								echo $count . ' ' . $dest_status . ($idx == 0 ? ", " : "");
							}
							echo ' in the last ' . formattedTimeElapsed($age) . '</p>';
						}
					}

					//echo '<pre>'.json_encode($activity, JSON_PRETTY_PRINT).'</pre>';
					?>
				</div>
				<?php endif; ?>

				<?php $fluxArray = $this->object->flux_sources(); if ( is_array($fluxArray) && count($fluxArray) > 0 ) : ?>
				<div>
					<h3>Download Activity</h3>
					<table width="100%">
						<tr><th></th><th>Created</th><th>Name</th><th>Source Status</th><th>Download Status</th></tr>

					<?php foreach( $fluxArray as $idx => $flux ) {
						echo '<tr>'
							. '<td><span class="icon ' . ($flux->flux_error ? 'true' : 'false') . '"></span></td>'
							. '<td><p>' . $flux->formattedDateTime_created() . '</p></td>'
							. '<td><p>' . $flux->name . '</p></td>'
							. '<td><p>' . $flux->src_status . '</p></td>'
							. '<td><p>' . $flux->dest_status . '</p></td>'
							.'</tr>';
					}
					?>
					</table>
				</div>

				<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</section>

<script type="text/javascript">
	$('body').on('click', 'a.test', function (e) {
		$.ajax({
			type: "GET",
			url: $(this).attr('data-href'),
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = $('#ajaxDiv');
				ajaxDisplay.empty().append(msg);
			}
		});
		e.stopPropagation();
		return false;
	});
</script>
