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

<div><!-- container -->
	<div><!-- top -->
		<div style="padding:15px; display:inline-block; top:0; width: auto; vertical-align:top;"><!-- right -->
			<form method="post" accept-charset="utf-8"
				action="<?php echo Config::Web($this->searchAction . (is_null($realObj) ? '' : '/' . $realObj->id) ); ?>"
				id="searchForm" name="searchForm">

				<fieldset>
				<legend><?php echo Localized::ModelLabel($this->model->tableName(),"SearchLegend"); ?></legend>

				<?php
					$attr = \model\media\Character::name;
					$attrName = $this->model->attributeName($realObj, null, $attr);
					$this->renderFormField( \Model::TEXT_TYPE, $realObj, null, $this->model, $attr, null, true );
				?>

				<div class="half">
					<input type="submit" name="edit_submit" value="<?php echo Localized::GlobalLabel("SearchButton"); ?>" />
				</div>

				<div class="half omega">
					<input type="reset" value="<?php echo Localized::GlobalLabel("ResetButton"); ?>"/>
				</div>
				</fieldset>
			</form>
		</div>
	</div>
	<div> <!-- bottom -->
		<div id='ajaxDiv'></div>
	</div>
</div><!-- container -->
