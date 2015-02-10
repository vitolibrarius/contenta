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
	$result_class = ($this->git_results['status'] == 0 ? 'success' : 'failure');
 ?>

<div id="upgrade_tests">
<ul>
	<li class="<?php echo $result_class; ?>"><div class="test_name">Results</div>
	<div>
		<div class="span_container">
			<div class="span_container_row">
				<span class="break">Checking status</span>
				<span class="break"><em>
					<?php if ( $this->git_results['status'] != 0 ) : ?>
						An error occurred: <?php echo $this->git_results['status']; ?>
					<?php endif; ?>
					<pre><?php echo $this->git_results['stdout'] ?></pre>
					<pre><?php echo $this->git_results['stderr'] ?></pre>
				</em></span>
			</div>
		</div>
	</div>
	</li>

</ul>
</div>
