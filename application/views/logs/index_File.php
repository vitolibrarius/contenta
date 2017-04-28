<form name='logFilter'>
	<fieldset>
	<legend>Filter Logs</legend>
	<table width="100%">
		<tr>
			<th>
				<select class="logFilter" id='filename'>
				<?php $dir = Config::instance()->loggingDirectory();
					$groups = array();
					if ( is_dir($dir) ) {
						foreach (scandir($dir) as $file) {
							if ($file == '.' || $file == '..') continue;
							$sz = formatSizeUnits( filesize( $dir . '/' . $file));
							$parts = preg_split('/_|-|\./', $file, -1, PREG_SPLIT_NO_EMPTY);
							if ( count($parts) != 5 ) {
								$groups[$file][$file] = $file . ' (' .$sz. ')';
							}
							else {
								$groupName = $parts[2] . " " . $parts[1];
								$groups[$groupName][$file] = $file . ' (' .$sz. ')';
							}
						}

						krsort($groups);
						foreach( $groups as $groupName => $values ) {
							echo "<optgroup label='" . $groupName . "'>";
							krsort($values);
							foreach( $values as $file => $display ) {
								echo "<option value='" . $file . "'>" . $display . "</option>";
							}
							echo "</optgroup>";
						}
					}
				?>
				</select>
			</th>
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
		search_timer = 0;
		$(".logFilter").on('keyup', function () {
			if (search_timer) {
				clearTimeout(search_timer);
			}
			search_timer = setTimeout(refresh, 1000);
		});
		function refresh() {
			var filename = document.getElementById('filename').value;
			var trace = document.getElementById('trace').value;
			var trace_id = document.getElementById('trace_id').value;
			var context = document.getElementById('context').value;
			var context_id = document.getElementById('context_id').value;
			var level = document.getElementById('level').value;
			var message = document.getElementById('message').value;
			var queryString = "?filename=" + encodeURIComponent(filename)
				+ "&trace=" + encodeURIComponent(trace) + "&trace_id=" + encodeURIComponent(trace_id)
				+ "&context=" + encodeURIComponent(context) + "&context_id=" + encodeURIComponent(context_id)
				+ "&level=" + encodeURIComponent(level) + "&message=" + encodeURIComponent(message);
			$.ajax({
				type: "GET",
				url: "<?php echo Config::Web('/logs/log_file'); ?>" + queryString,
				success: function(msg){
					var ajaxDisplay = document.getElementById('ajaxDiv');
					if ( ajaxDisplay.innerHTML != msg ) {
						ajaxDisplay.innerHTML = msg;
					}
				}
			});
		};
		refresh();
// 		setInterval (function f() {
// 			refreshJobs();
// 		}, 50000);
	});
</script>
