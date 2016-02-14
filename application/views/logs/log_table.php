<style>
	table.Log {border-collapse:collapse; }
	table.Log tr td {background-color:#fff; word-wrap:break-word;}
	table.Log tr.warning td {background-color:#FAF1F3;}
	table.Log tr.error td {background-color:#F9CACA;}
	table.Log tr.fatal td {background-color:#f2888c;}
</style>

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
			echo '<tr class="' . $log->level . '">'
					. '<td rowspan="2"><nobr>' . $log->formattedDate("created", 'M d, Y') . '</nobr><br/><nobr>'
							. $log->formattedDate("created", "H:i") . '</nobr></td>'
					. '<td rowspan="2">' . $log->level . '</td>'
					. '<td>' . $log->trace . '</td>'
					. '<td>' . $log->context . '</td>'
					. '<td rowspan="2" class="log_msg" valign="top"><pre>' . $log->message . '</pre></td>'
					. '</tr>';
			echo '<tr class="' . $log->level . '">'
					. '<td>' . $log->trace_id . '</td>'
					. '<td>' . $log->context_id . '</td>'
					. '</tr>';

		}
	?>
	</table>
</div>
