<style>
	table.Log {border-collapse:collapse; }
	table.Log tr td {background-color:#fff; word-wrap:break-word;}
	table.Log tr.warning td {background-color:#FAF1F3;}
	table.Log tr.error td {background-color:#F9CACA;}
	table.Log tr.fatal td {background-color:#f2888c;}
	table.Log td {
		white-space: pre-wrap;       /* css-3 */
		white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
		white-space: -pre-wrap;      /* Opera 4-6 */
		white-space: -o-pre-wrap;    /* Opera 7 */
		word-wrap: break-word;       /* Internet Explorer 5.5+ */
		word-break: break-all;
	}
</style>

<div class="paging">
	<ul>
	<?php $min = 0; $max = $this->chunkCount;
	if ($this->pageCurrent > 5) {
		$min = $this->pageCurrent - 5;
	}
	if ($max - $this->pageCurrent > 5 ) {
		$max = $this->pageCurrent + 5;
	}
	if ( $min > 0 ) {
		echo '<li><a href="#">First</a></li><li>....</li>';
	}
	for ($x = $min; $x < $max; $x++) {
		if ( $x == $this->pageCurrent ) {
			echo '<li class="current">';
		}
		else {
			echo '<li>';
		}
		echo '<a href="#">' . $x . '</a></li>';
	}
	if ( $max < $this->chunkCount -1 ) {
		echo '<li>....</li><li><a href="#">' . $this->chunkCount . '</a></li>';
	}
	?>
	</ul>
</div>

	<h2><?php echo (isset($this->filename) ? $this->filename : "no file"); ?></h2>

	<h2><?php echo (isset($this->chunkCount) ? $this->chunkCount : "nothing"); ?></h2>

<div class="mediaData">
	<table class="Log">
		<tr>
			<th>Date</th>
			<th>Level</th>
			<th>Trace</th>
			<th>Context</th>
			<th>Message</th>
		</tr>
	<?php
		foreach ($this->logArray as $key => $log) {
			if ( is_string($log) ) {
				echo "<tr><td colspan='5'><pre>" . $log . "</pre></td></tr>";
			}
			else {
				echo "<tr class='" . $log['type'] . "'><td>" . $log['time'] . "</td>"
					. "<td>" . $log['type'] . "</td>"
					. "<td>" . $log['trace'] . " " . $log['trace_id'] . "</td>"
					. "<td>" . $log['context'] . " " . $log['context_id'] . "</td>"
					. "<td>" . $log['message'] . "</td>"
					. "</tr>";
			}
		}
	?>
	</table>
</div>
