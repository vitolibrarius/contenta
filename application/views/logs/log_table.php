<style type="text/css">
	table.Log {border-collapse:collapse; }
	table.Log tr td {background-color:#fff; word-wrap:break-word;}
	table.Log tr.warning td {background-color:#FAF1F3;}
	table.Log tr.error td {background-color:#F9CACA;}
	table.Log tr.fatal td {background-color:#f2888c;}
</style>

<section>
	<div class="row"><div class="grid_12">
<?php use \html\Paginator as Paginator;
	if ( isset($this->params) ) {
		$p = new Paginator( $this->params, Config::Web('/logs/log_table') );
		echo $p->render();
	}
	else {
		echo "<b> no parameters</b>";
	}
?>
	</div></div>
</section>


<div class="mediaData">
<?php if (is_array($this->logArray) && count($this->logArray) > 0): ?>
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
			echo '<tr class="' . $log->level_code . '">'
					. '<td rowspan="2"><nobr>' . $log->formattedDate("created", 'M d, Y') . '</nobr><br/><nobr>'
							. $log->formattedDate("created", "H:i") . '</nobr></td>'
					. '<td rowspan="2">' . $log->level_code . '</td>'
					. '<td>' . $log->trace . '</td>'
					. '<td>' . $log->context . '</td>'
					. '<td rowspan="2" class="log_msg" valign="top"><pre>' . htmlentities($log->message) . '</pre></td>'
					. '</tr>';
			echo '<tr class="' . $log->level_code . '">'
					. '<td>' . $log->trace_id . '</td>'
					. '<td>' . $log->context_id . '</td>'
					. '</tr>';

		}
	?>
	</table>
<?php else: ?>
	<div class="log_row info">
		<span class="log_date"><?php echo date('M d, Y  H:i', time()); ?></span>
		<span class="log_level">info</span>
		<span class="log_msg"><em>No Log messages found</em></span>
	</div>
<?php endif; ?>
</div>
