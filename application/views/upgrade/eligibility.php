<style type="text/css">
#upgrade_tests ul { list-style-type: none; }

#upgrade_tests li {
	border-top: 1px dotted #999;
	border-width: 1px 0;
	margin: 5px;
	padding-left: 20px;
	background-repeat: no-repeat;
	background-position: 0 .5em;
}

#upgrade_tests li.success {
	background-image: url(<?php echo Config::Web('/public/img/success_sm.png'); ?>);
}

#upgrade_tests li.warning {
	background-image: url(<?php echo Config::Web('/public/img/alert_sm.png'); ?>);
}

#upgrade_tests li.failure {
	background-image: url(<?php echo Config::Web('/public/img/failure_sm.png'); ?>);
}

#upgrade_tests li .test_name {
	color: #990000;
	display: block;
	font: bold 120% Arial, Helvetica, sans-serif;
	padding: 5px;
	text-decoration: none;
}

#upgrade_tests p,
#upgrade_tests pre
{
	color: #333;
	display: block;
	margin: 5px;
	padding-top: 4px;
}
</style>

<?php
	$whoami = trim(`whoami`);

	$git = new utilities\Git(SYSTEM_PATH);
	$files = $git->checkRepositoryOwnership(10, array("contenta.ini", ".htaccess", ".DS_Store") );
	$file_test_class = ($files['status'] == 0 ? 'success' : 'failure');
	$git_test = $git->status();
	$git_remote_test = $git->remoteStatus();
	$git_remote_class = ($git_remote_test == 'Up-to-date' ? 'success' : 'failure');
	$git_test_out = preg_split('/\n|\r/', $git_test['stdout'], -1, PREG_SPLIT_NO_EMPTY);
	$git_test_class = ($git_test['status'] == 0 && count($git_test_out) == 1 ? 'success' : 'failure');
	$git_eligible = ($files['status'] == 0 && $git_test['status'] == 0 && count($git_test_out) == 1);
 ?>

<div id="upgrade_tests">
<ul>
	<li class="<?php echo $file_test_class; ?>"><div class="test_name">File Permissions</div>
	<div>
		<p><em>Testing to ensure the files in the install application directory may be upgraded.</em></p>
		<div class="span_container">
			<div class="span_container_row">
				<span class="break"><?php echo Config::AppName() . " is executing as user "; ?></span>
				<span class="break"><em><?php echo $whoami; ?></em></span>
			</div>
			<div class="span_container_row">
				<span class="break">Checking ownership of application files</span>
				<span class="break"><em>
					<?php if ( $files['status'] == 0) : ?>
						ownership looks good
					<?php else : ?>
						Some files are not owned by the executing user.  This is necessary for the files to be updated.
						the unix commands to change the ownership would be
						<pre>$ cd <?php echo dirname(SYSTEM_PATH); ?></pre>
						<pre>$ sudo chown -R <?php echo $whoami . ' ' . basename(SYSTEM_PATH); ?></pre>
						The first 10 files that
						are not owned properly are:
						<pre><?php echo var_export($files['badFiles'], true); ?></pre>
					<?php endif; ?>
				</em></span>
			</div>
		</div>
	</div>
	</li>

	<li class="<?php echo $git_test_class; ?>"><div class="test_name">GIT local repository status</div>
	<div>
		<p><em></em></p>
		<div class="span_container">
			<div class="span_container_row">
				<span class="break">Checking status</span>
				<span class="break"><em>
					<?php if ( $git_test['status'] == 0 && count($git_test_out) == 1 ) : ?>
						Repository clean
					<?php elseif ($git_test['status'] != 0) : ?>
						An error occurred: <?php echo $git_test['status']; ?>
						<pre><?php echo $git_test['stdout'] ?></pre>
						<pre><?php echo $git_test['stderr'] ?></pre>
					<?php else : ?>
						You have uncommitted changes:
						<pre><?php echo $git_test['stdout'] ?></pre>
						<pre><?php echo $git_test['stderr'] ?></pre>
					<?php endif; ?>
				</em></span>
			</div>
		</div>
	</div>
	</li>

	<li class="<?php echo $git_remote_class; ?>"><div class="test_name">GIT remote repository status</div>
	<div>
		<p><em></em></p>
		<div class="span_container">
			<div class="span_container_row">
				<span class="break">Checking status</span>
				<span class="break"><em>
						<pre><?php echo $git_remote_test ?></pre>
				</em></span>
			</div>
		</div>
	</div>
	</li>

	<?php if (isset($this->patches) && is_array($this->patches)): ?>
	<li class="success"><div class="test_name">Applied Versions</div>
	<div>
		<p><em></em></p>
		<div class="span_container">
			<div class="span_container_row header">
				<span class="break"><?php echo Localized::ModelLabel("version", "code" ); ?></span>
				<span class="break"><?php echo Localized::ModelLabel($this->model->tableName(), "name" ); ?></span>
				<span class="break"><?php echo Localized::ModelLabel($this->model->tableName(), "created" ); ?></span>
			</div>

		<?php foreach($this->patches as $key => $value): ?>
				<div class="span_container_row">
					<span class="break"><?php echo $value->version()->code; ?></span>
					<span class="break"><em>
							<pre><?php echo htmlentities($value->name) ?></pre>
					</em></span>
					<span class="break"><?php echo $value->formattedDateTime_created(); ?></span>
				</div>
		<?php endforeach; ?>
		</div>
	</div>
	</li>
	<?php endif; ?>
</ul>

	<?php if ( $git_eligible == true ) : ?>
	<div class="paging">
		<ul>
			<li><a class="confirm" href="#">Ready to upgrade</a></li>
		</ul>
	</div>
	<?php endif; ?>
</div>

<script>
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		var list = $('a.confirm');
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo $this->label("modal", "Confirm Upgrade"); ?>',
				img: '<?php echo Config::Web("/public/img/Logo_sm.png"); ?>',
				description: '<?php echo $this->label( "eligibility", "UpgradeDescription"); ?>',
				confirm: '<?php echo $this->label( "eligibility", "UpgradeConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("UpgradeButton"); ?>',
				action: '<?php echo Config::Web("/Upgrade/gitPull"); ?>'
			});
			e.preventDefault();
		});
	});
</script>
