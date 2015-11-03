<script language="javascript" type="text/javascript">
<!--
$(function () {
	$("#searchForm").submit(function (e) {
	    var postData = $(this).serializeArray();
	    var formURL = $(this).attr("action");
	    $.ajax(
	    {
	        url : formURL,
	        type: "POST",
	        data : postData,
	        success:function(data, textStatus, jqXHR)
	        {
				var ajaxDisplay = document.getElementById('ajaxDiv');
				ajaxDisplay.innerHTML = data;
	        },
	        error: function(jqXHR, textStatus, errorThrown)
	        {
				ajaxDisplay.innerHTML = textStatus . errorThrown;
	        }
	    });
	    e.preventDefault(); //STOP default action
	});
});
//-->
</script>

<?php
$realObj = (isset($this->object)) ? $this->object : null;
?>

<h1 class="group">pub query</h1>
<!-- top ($xid = null, $vol = null, $name = null, $aliases = null, $coverYear = null, $issue_number = null) -->
<section>
	<form method="post" accept-charset="utf-8"
		action="<?php echo Config::Web($this->searchAction . (is_null($realObj) ? '' : '/' . $realObj->id) ); ?>"
		id="searchForm" name="searchForm">
	<div class="row">
		<div class="grid_2">
			<input type="number" name="searchxid" id="searchxid"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "xid" ); ?>"
				value="">
		</div>
		<div class="grid_3">
			<input type="text" name="searchseries_name" id="searchseries_name"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch("series", "name" ); ?>"
				value="">
		</div>
		<div class="grid_1">
			<input type="text" name="searchissue" id="searchissue"
				class="text_input"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "issue_display" ); ?>"
				value="">
		</div>
		<div class="grid_1">
			<input type="number" name="searchYear" id="searchYear"
				class="text_input"
				min="1950"
				max="<?php echo intval(date("Y") + 1); ?>"
				placeholder="<?php echo Localized::ModelSearch($this->model->tableName(), "year" ); ?>"
				value="">
		</div>
		<div class="grid_1">
			<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("SearchButton"); ?>" />
		</div>
	</div>
	</form>
</section>

		<div id='ajaxDiv'></div>
