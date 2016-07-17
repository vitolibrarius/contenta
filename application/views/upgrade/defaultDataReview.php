<style type="text/css">
.DataReview h3,
.DataReview h4,
.DataReview h5 {
	background-color: #ffffee;
	padding: .5em;
	margin: 0;
}
.DataReview .newznab {
	margin: 2px;
	padding: 2px;
}
.DataReview .desc {
	font-size: 0.75em;
	color: #333;
	display: block;
	margin: 5px;
	padding-left: 4px;
}
</style>

<?php
	$endpoint_model = Model::Named("Endpoint");
	$endpoint_type_model = Model::Named("Endpoint_Type");
	$job_model = Model::Named("Job");
	$job_type_model = Model::Named("Job_Type");
?>

<section class="DataReview">
<div class="row">

<?php
	$cvType = $endpoint_type_model->ComicVine();
	$points = $endpoint_model->allForTypeCode("ComicVine");
	$comicVinePass = (is_array($points) && count($points) > 0);
	$comicVineMultiplePass = (is_array($points) && count($points) == 1);
	$cv = (is_array($points) && count($points) > 0) ? $points[0] : false;
	$comicVineAPIPass = ($cv != false && strlen($cv->api_key) > 0);
?>
<!-- comicvine card -->
<div class="grid_4">
	<div class="card">
		<h3>ComicVine</h3>
		<div class="desc"><p><?php echo $cvType->comments; ?></p></div>

		<div>
			<div style="white-space: nowrap;">
			<?php if ( $comicVinePass ) : ?>
				<span class="icon true"></span> Endpoint Available
			<?php else : ?>
				<a href="<?php echo Config::Web('/Netconfig/edit_new/', $cvType->code);?>" class="button">
					Create Endpoint
				</a>
			<?php endif; ?>
			</div>

			<?php if ( $comicVinePass && $comicVineMultiplePass == false) : ?>
			<div>
				<span class="icon false"></span> Multiple ComicVine connections are not permitted by ComicVine.
					You will get IP banned for missuse.
			</div>
			<?php endif; ?>

			<?php if ( $comicVinePass ) : ?>
			<div style="white-space: nowrap;">
				<?php if ( $comicVineAPIPass) : ?>
					<span class="icon true"></span> API Configured
				<?php else : ?>
					<a href="<?php echo $cvType->api_url; ?>">Register for API Key</a>

					<a href="<?php echo Config::Web('/Netconfig/edit/', $points[0]->id);?>" class="button job">
						Edit <?php echo $points[0]->name; ?> Endpoint
					</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>

		<h4>Jobs Available</h4>
		<?php if ( $comicVinePass && $comicVineAPIPass ) : ?>
			<div>
			<?php $list = array("publisher","series","character","story_arc"); foreach ( $list as $type ) : ?>
			<?php $jobType = $job_type_model->objectForCode($type);
				$jobs = $job_model->allForType_code($type); $jobPass = (is_array($jobs) && count($jobs) > 0); ?>
				<span class="desc"><?php echo $jobType->desc; ?></span>
				<div style="white-space: nowrap;">
				<?php if ( $jobPass ) : ?>
					<span class="icon true"></span> <?php echo ucfirst($type); ?> Job
				<?php else : ?>
					<a href="<?php echo Config::Web('/Upgrade/createJob/', $type);?>" class="button job">
						Create <?php echo ucfirst($type); ?> Job
					</a>
				<?php endif; ?>
				</div>
			<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="desc"><p>An Endpoint configuration for <?php echo $cvType->name; ?>
				is required before jobs can be scheduled</p></div>
		<?php endif; ?>
	</div>
</div>

<!-- sabnzbd card -->
<?php
	$sabnzbdType = $endpoint_type_model->SABnzbd();
	$points = $endpoint_model->allForTypeCode($sabnzbdType->code);
	$sabnzbdPass = (is_array($points) && count($points) > 0);
	$sabnzbd = (is_array($points) && count($points) > 0) ? $points[0] : false;
	$sabnzbdAPIPass = ($sabnzbd != false && strlen($sabnzbd->api_key) > 0);
?>
<div class="grid_4">
	<div class="card">
		<h3>SABnzbd</h3>
		<div class="desc"><p><?php echo $sabnzbdType->comments; ?></p></div>

		<div>
			<div style="white-space: nowrap;">
			<?php if ( $sabnzbdPass ) : ?>
				<span class="icon true"></span> Endpoint Available
			<?php else : ?>
				<a href="<?php echo Config::Web('/Netconfig/edit_new/', $sabnzbdType->code);?>" class="button">
					Create Endpoint
				</a>
			<?php endif; ?>
			</div>
		</div>

		<div>
			<div style="white-space: nowrap;">
			<?php if ( $sabnzbdAPIPass ) : ?>
				<span class="icon true"></span> API Configured
			<?php else : ?>
				<a href="<?php echo $sabnzbd->base_url; ?>">Register for API Key</a>

				<a href="<?php echo Config::Web('/Netconfig/edit/', $points[0]->id);?>" class="button">
					Edit <?php echo $points[0]->name; ?> Endpoint
				</a>
			<?php endif; ?>
			</div>
		</div>

		<h4>Jobs Available</h4>
		<?php if ( $sabnzbdPass ) : ?>
			<?php $list = array("sabnzbd"); foreach ( $list as $type ) : ?>
				<?php $jobType = $job_type_model->objectForCode($type);
					$jobs = $job_model->allForType_code($type); $jobPass = (is_array($jobs) && count($jobs) > 0); ?>
				<span class="desc"><?php echo $jobType->desc; ?></span>
				<div style="white-space: nowrap;">
				<?php if ( $jobPass ) : ?>
					<span class="icon true"></span> <?php echo ucfirst($type); ?> Job
				<?php else : ?>
					<a href="<?php echo Config::Web('/Upgrade/createJob/', $type);?>" class="button job">
						Create <?php echo ucfirst($type); ?> Job
					</a>
				<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="desc"><p>An Endpoint configuration for <?php echo $sabnzbdType->name; ?>
					is required before jobs can be scheduled</p></div>
		<?php endif; ?>
	</div>
</div>

<!-- Newznab card -->
<?php
	$NewznabType = $endpoint_type_model->Newznab();
	$points = $endpoint_model->allForTypeCode($NewznabType->code);
	$NewznabPass = (is_array($points) && count($points) > 0);
?>
<div class="grid_4">
	<div class="card">
		<h3>Newznab</h3>
		<div class="desc"><p><?php echo $NewznabType->comments; ?></p></div>

		<div>
			<div style="white-space: nowrap;">
			<?php if ( $NewznabPass ) : ?>
				<span class="icon true"></span> Endpoint(s) Available
			<?php else : ?>
				<a href="<?php echo Config::Web('/Netconfig/edit_new/', $NewznabType->code);?>" class="button">
					Create Endpoint
				</a>
			<?php endif; ?>
			</div>
		</div>

		<?php if ( $NewznabPass ) : ?>
			<?php foreach ($points as $newznabPoint) : ?>
			<div class="newznab">
				<h5><?php echo $newznabPoint->name; ?></h5>
				<?php if ( strlen($newznabPoint->api_key) > 0 ) : ?>
					<span class="icon true"></span> API Configured
				<?php else : ?>
					<a href="<?php echo Config::Web('/Netconfig/edit/', $newznabPoint->id);?>" class="button">
						<?php echo $newznabPoint->name; ?> API Key is required
					</a>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>

			<h4>Jobs Available</h4>
			<?php $list = array("newznab_search"); foreach ( $list as $type ) : ?>
				<?php $jobType = $job_type_model->objectForCode($type);
					$jobs = $job_model->allForType_code($type); $jobPass = (is_array($jobs) && count($jobs) > 0); ?>
				<span class="desc"><?php echo $jobType->desc; ?></span>
				<div style="white-space: nowrap;">
				<?php if ( $jobPass ) : ?>
					<span class="icon true"></span> <?php echo ucfirst($type); ?> Job
				<?php else : ?>
					<a href="<?php echo Config::Web('/Upgrade/createJob/', $type);?>" class="button job">
						Create <?php echo ucfirst($type); ?> Job
					</a>
				<?php endif; ?>
				</div>
			<?php endforeach; ?>

		<?php else : ?>
			<div class="desc"><p>An Endpoint configuration for Newznab is required before jobs can be scheduled</p></div>
		<?php endif; ?>
	</div>
</div>

<!-- rss card -->
<?php
	$rssType = $endpoint_type_model->RSS();
	$points = $endpoint_model->allForTypeCode($rssType->code);
	$rssPass = (is_array($points) && count($points) > 0);
?>
<div class="grid_4">
	<div class="card">
		<h3>RSS</h3>
		<div class="desc"><p><?php echo $rssType->comments; ?></p></div>

		<div>
			<div style="white-space: nowrap;">
			<?php if ( $rssPass ) : ?>
				<span class="icon true"></span> Endpoint(s) Available
			<?php else : ?>
				<a href="<?php echo Config::Web('/Upgrade/createRss/');?>" class="button">
					Create binsearch.net Endpoint
				</a>
			<?php endif; ?>
			</div>
		</div>

		<h4>Jobs Available</h4>
		<?php if ( $rssPass ) : ?>
			<?php foreach ($points as $rssPoint) : ?>
			<div>
				<h5><?php echo $rssPoint->name; ?></h5>
				<?php $list = array("rss"); foreach ( $list as $type ) : ?>
					<?php $jobType = $job_type_model->objectForCode($type);
						$jobs = $job_model->allForType_code($type); $jobPass = (is_array($jobs) && count($jobs) > 0); ?>
					<span class="desc"><?php echo $jobType->desc; ?></span>
					<div style="white-space: nowrap;">
					<?php if ( $jobPass ) : ?>
						<span class="icon true"></span> <?php echo $rssPoint->name; ?> Job
					<?php else : ?>
						<a href="<?php echo Config::Web('/Upgrade/createJob/', $type, $rssPoint->id);?>" class="button job">
							Create <?php echo $rssPoint->name; ?> Job
						</a>
					<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="desc"><p>An Endpoint configuration for <?php echo $rssType->name; ?>
				is required before jobs can be scheduled</p></div>
		<?php endif; ?>
	</div>
</div>

<!-- previewsworld card -->
<?php
	$pwType = $endpoint_type_model->PreviewsWorld();
	$points = $endpoint_model->allForTypeCode($pwType->code);
	$PreviewsWorldPass = (is_array($points) && count($points) == 1);
?>
<div class="grid_4">
	<div class="card">
		<h3>PREVIEWSworld</h3>
		<div class="desc"><p><?php echo $pwType->comments; ?></p></div>

		<div>
			<div style="white-space: nowrap;">
			<?php if ( $PreviewsWorldPass ) : ?>
				<span class="icon true"></span> Endpoint Available
			<?php else : ?>
				<a href="<?php echo Config::Web('/Netconfig/edit_new/', $pwType->code);?>" class="button">
					Create Endpoint
				</a>
			<?php endif; ?>
			</div>
		</div>

		<h4>Jobs Available</h4>
		<?php if ( $PreviewsWorldPass ) : ?>
			<?php $list = array("previewsworld"); foreach ( $list as $type ) : ?>
				<?php $jobType = $job_type_model->objectForCode($type);
					$jobs = $job_model->allForType_code($type); $jobPass = (is_array($jobs) && count($jobs) > 0); ?>
				<span class="desc"><?php echo $jobType->desc; ?></span>
				<div style="white-space: nowrap;">
				<?php if ( $jobPass ) : ?>
					<span class="icon true"></span> <?php echo ucfirst($type); ?> Job
				<?php else : ?>
					<a href="<?php echo Config::Web('/Upgrade/createJob/', $type);?>" class="button job">
						Create <?php echo ucfirst($type); ?> Job
					</a>
				<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="desc"><p>An Endpoint configuration for <?php echo $pwType->name; ?>
				is required before jobs can be scheduled</p></div>
		<?php endif; ?>
	</div>
</div>

<!-- Other card -->
<div class="grid_4">
	<div class="card">
		<h3>Other Jobs</h3>
		<div class="desc"><p>Other scheduled activities</p></div>

		<h4>Jobs Available</h4>
		<?php $list = array("reprocessor"); foreach ( $list as $type ) : ?>
			<?php $jobType = $job_type_model->objectForCode($type);
				$jobs = $job_model->allForType_code($type); $jobPass = (is_array($jobs) && count($jobs) > 0); ?>
			<span class="desc"><?php echo $jobType->desc; ?></span>
			<div style="white-space: nowrap;">
			<?php if ( $jobPass ) : ?>
				<span class="icon true"></span> <?php echo ucfirst($type); ?> Job
			<?php else : ?>
				<a href="<?php echo Config::Web('/Upgrade/createJob/', $type);?>" class="button job">
					Create <?php echo ucfirst($type); ?> Job
				</a>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>

	</div>
</div>


</div> <!-- row -->
</section>
