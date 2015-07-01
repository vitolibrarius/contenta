<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminJobs/index'); ?>"><span class="">Job Schedules</span></a></li>
		<li><a href="<?php echo Config::Web('/AdminJobs/edit'); ?>"><span class="">Add New Job</span></a></li>
	</ul>
</div>

<div id='ajaxDiv'></div>

<script language="javascript" type="text/javascript">
	$(document).ready(function($) {
		function refreshJobs() {
			$.ajax({
				type: "GET",
				url: "<?php echo Config::Web('/AdminJobs/ajax_runningTable'); ?>",
				success: function(msg){
					var ajaxDisplay = document.getElementById('ajaxDiv');
					if ( ajaxDisplay.innerHTML != msg ) {
						ajaxDisplay.innerHTML = msg;
					}
				}
			});
		};
		refreshJobs();
		setInterval (function f() {
			refreshJobs();
		}, 5000);
	});
</script>
