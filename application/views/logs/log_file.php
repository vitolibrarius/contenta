<style>
	table.Log {border-collapse:collapse; }
	table.Log tr td {background-color:#fff; word-wrap:break-word;}
	table.Log tr.warning td {background-color:#FAF1F3;}
	table.Log tr.error td {background-color:#F9CACA;}
	table.Log tr.fatal td {background-color:#f2888c;}
</style>

	<h2><?php echo (isset($this->filename) ? $this->filename : "no file"); ?></h2>

	<h2><?php echo (isset($this->elapsed) ? $this->elapsed : "nothing"); ?></h2>

<div class="mediaData">
	<table class="Log">
		<tr>
			<th>Date</th>
			<th>Level</th>
			<th>Message</th>
			<th>Trace</th>
			<th>Context</th>
		</tr>
	<?php
		foreach ($this->logArray as $key => $log) {
			if ( is_string($log) ) {
				echo "<tr><td colspan='5'><pre>" . $log . "</pre></td></tr>";
			}
			else {
				echo "<tr class='" . $log['type'] . "'><td>" . $log['time'] . "</td>"
					. "<td>" . $log['type'] . "</td>"
					. "<td>" . $log['message'] . "</td>"
					. "<td>" . $log['trace'] . " " . $log['trace_id'] . "</td>"
					. "<td>" . $log['context'] . " " . $log['context_id'] . "</td>"
					. "</tr>";
			}
		}
	?>
	</table>
</div>
