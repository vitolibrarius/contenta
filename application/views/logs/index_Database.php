<form name='logFilter'>
	<fieldset>
	<legend>Filter Logs</legend>
	<table width="100%">
		<tr>
			<th>
				<select class="logFilter" id='level'>
					<option value="any" selected>Any</option>
					<option value="info">Info</option>
					<option value="warning">Warning</option>
					<option value="error">Error</option>
					<option value="fatal">Fatal</option>
				</select>
			</th>
			<th>
				<input class="logFilter" type='text' id='trace' placeholder="Trace Name"/>
			</th>
			<th>
				<input class="logFilter" type='text' id='trace_id' placeholder="Unique Trace Id"/>
			</th>
			<th>
				<input class="logFilter" type='text' id='context' placeholder="Context name"/>
			</th>
			<th>
				<input class="logFilter" type='text' id='context_id' placeholder="Unique Context id" />
			</th>
			<th>
				<input class="logFilter" type='text' id='message' placeholder="log message content" />
			</th>
		</tr>
	</table>
	</fieldset>
</form>

<div id='ajaxDiv'></div>

<script language="javascript" type="text/javascript">
	$(document).ready(function($) {
		$(".logFilter").on('keyup change', function () {
			delay( refreshJobs(), 250 );
		});
		function refreshJobs() {
			var trace = document.getElementById('trace').value;
			var trace_id = document.getElementById('trace_id').value;
			var context = document.getElementById('context').value;
			var context_id = document.getElementById('context_id').value;
			var level = document.getElementById('level').value;
			var message = document.getElementById('message').value;
			var queryString = "?trace=" + encodeURIComponent(trace) + "&trace_id=" + encodeURIComponent(trace_id)
				+ "&context=" + encodeURIComponent(context) + "&context_id=" + encodeURIComponent(context_id)
				+ "&level=" + encodeURIComponent(level) + "&message=" + encodeURIComponent(message);
			$.ajax({
				type: "GET",
				url: "<?php echo Config::Web('/logs/log_table'); ?>" + queryString,
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
		}, 10000);
	});
</script>
