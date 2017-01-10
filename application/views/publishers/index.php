<script>
	// Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		var list = $('a.confirm');
		$('a.confirm').click(function(e){
			modal.open({
				heading: '<?php echo Localized::GlobalLabel("Modal", "Confirm Delete"); ?>',
				img: '<?php echo Config::Web("/public/img/Logo_sm.png"); ?>',
				description: '<?php echo $this->label( "index", "DeleteDescription"); ?>',
				confirm: '<?php echo $this->label( "index", "DeleteConfirmation"); ?>',
				actionLabel: '<?php echo Localized::GlobalLabel("DeleteButton"); ?>',
				action: $(this).attr('action')
			});
			e.preventDefault();
		});
	});
</script>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminCharacters/index'); ?>">Characters</a></li>
		<li><a href="<?php echo Config::Web('/AdminSeries/index'); ?>">Series</a></li>
		<li><a href="<?php echo Config::Web('/AdminStoryArcs/index'); ?>">Story Arcs</a></li>
		<li><a href="<?php echo Config::Web('/AdminPublication/index'); ?>">Publications</a></li>
	</ul>
</div>

<section>
	<form id='searchForm' name='searchForm'>
	<div class="row">
		<div class="grid_3">
			<input class="text_input" type='text' id='name' name='name' placeholder="Name"
				value="<?php echo (isset($this->params) ? $this->params->valueForKey('name') : ''); ?>"
			/>
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
			var page_url = "<?php echo Config::Web('/AdminPublishers/publisherList'); ?>";
			var resultsId = "ajaxDiv";
			var inputValues = $("form#searchForm").serializeObject();

			refreshAjax( page_url, undefined, inputValues, resultsId );
		};
		refresh();
	});
</script>

