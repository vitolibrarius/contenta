<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<select class="logFilter" id='level' name='level_code'>
				<option value="any" <?php if (isset($this->params) && $this->params->valueForKey('level_code') == 'any') { echo "selected";} ?>
				>Any</option>
				<option value="info" <?php if (isset($this->params) && $this->params->valueForKey('level_code') == 'info') { echo "selected";} ?>
				>Info</option>
				<option value="warning" <?php if (isset($this->params) && $this->params->valueForKey('level_code') == 'warning') { echo "selected";} ?>
				>Warning</option>
				<option value="error" <?php if (isset($this->params) && $this->params->valueForKey('level_code') == 'error') { echo "selected";} ?>
				>Error</option>
				<option value="fatal" <?php if (isset($this->params) && $this->params->valueForKey('level_code') == 'fatal') { echo "selected";} ?>
				>Fatal</option>
			</select>
		</div>
		<div class="grid_3">
			<input class="text_input" type='text' id='trace' name='trace' placeholder="Trace Name"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('trace') : ''); ?>"
			/>
			<input class="text_input" type='text' id='trace_id' name='trace_id' placeholder="Unique Trace Id"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('trace_id') : ''); ?>"
			/>
		</div>
		<div class="grid_3">
			<input class="text_input" type='text' id='context' name='context' placeholder="Context name"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('context') : ''); ?>"
			/>
			<input class="text_input" type='text' id='context_id' name='context_id' placeholder="Unique Context id"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('context_id') : ''); ?>"
			/>
		</div>
		<div class="grid_3">
			<input class="text_input" type='text' id='message' name="message" placeholder="log message content"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('message') : ''); ?>"
			/>
			<a href="<?php echo Config::Web('/logs/purgeMatches'); ?>" class="" style="white-space:nowrap;">Purge</a>
		</div>
	</div>
	</form>
</section>


<div id='ajaxDiv'></div>

<script language="javascript" type="text/javascript">
	$(document).ready(function($) {
		search_timer = 0;

		$(".text_input").on('keyup', function () {
			if (search_timer) {
				clearTimeout(search_timer);
			}
			search_timer = setTimeout(refresh, 400);
		});
		function refresh() {
			var page_url = "<?php echo Config::Web('/logs/log_table'); ?>";
			var resultsId = "ajaxDiv";
			var inputValues = $("form#searchForm").serializeObject();

			refreshAjax( page_url, undefined, inputValues, resultsId );
		};
		refresh();
	});
</script>
