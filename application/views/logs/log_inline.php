<div class="log_container">
<?php if (is_array($this->logArray) && count($this->logArray) > 0): ?>
	<?php foreach ($this->logArray as $key => $log): ?>
		<div class="log_row <?php echo $log->level; ?>">
			<span class="log_date"><?php echo date('M d, Y  H:i', $log->created); ?></span>
			<span class="log_level"><?php echo $log->level; ?></span>
			<span class="log_msg"><?php echo $log->message; ?></span>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="log_row info">
		<span class="log_date"><?php echo date('M d, Y  H:i', time()); ?></span>
		<span class="log_level">info</span>
		<span class="log_msg"><em>No Log messages found</em></span>
	</div>
<?php endif; ?>
</div>
