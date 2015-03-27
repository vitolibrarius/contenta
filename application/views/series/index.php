<script language="javascript" type="text/javascript">
	// Wait until the DOM has loaded before querying the document
	$(document).ajaxComplete(function(){
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

<script language="javascript" type="text/javascript">
	$(function () {
		$('#searchForm').submit(function(e){
			e.preventDefault();
			$.ajax({
				type: "GET",
				url: "<?php echo Config::Web('/AdminSeries/searchSeries'); ?>",
				data: {name: $('input#searchField').val()},
				dataType: "text",
				success: function(msg){
					var ajaxDisplay = document.getElementById('ajaxDiv');
					ajaxDisplay.innerHTML = msg;
				}
			});
		});
		$("#searchField").keyup(function () {
			delay( function(){
				$.ajax({
					type: "GET",
					url: "<?php echo Config::Web('/AdminSeries/searchSeries'); ?>",
					data: {name: $('input#searchField').val()},
					dataType: "text",
					success: function(msg){
						var ajaxDisplay = document.getElementById('ajaxDiv');
						ajaxDisplay.innerHTML = msg;
					}
				});
			}, 1000 );
		});
	});
</script>

<div class="paging">
	<ul>
		<li><a href="<?php echo Config::Web('/AdminPublishers/index'); ?>">Publishers</a></li>
		<li><a href="<?php echo Config::Web('/AdminCharacters/index'); ?>">Characters</a></li>
		<li><a href="<?php echo Config::Web( '/AdminSeries/comicVineSearch' ); ?>"><span class="">ComicVine Import</span></a></li>
	</ul>
</div>

<form id='searchForm' name='searchForm' style="display: block; ">
	<input type="text" name="searchField" id="searchField"
		placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "name" ); ?>"
		value="">
</form>

<div id='ajaxDiv'></div>

<script type="text/javascript">
$(document).ready(function($) {
	$('#searchField').trigger('keyup');
});
</script>
