<script language="javascript" type="text/javascript">
<!--
//Browser Support Code
function ajaxFunction(){
 var ajaxRequest;  // The variable that makes Ajax possible!

 try{
   // Opera 8.0+, Firefox, Safari
   ajaxRequest = new XMLHttpRequest();
 }catch (e){
   // Internet Explorer Browsers
   try{
	  ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
   }catch (e) {
	  try{
		 ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
	  }catch (e){
		 // Something went wrong
		 alert("Your browser broke!");
		 return false;
	  }
   }
 }

 // Create a function that will receive data
 // sent from the server and will update
 // div section in the same page.
 ajaxRequest.onreadystatechange = function(){
   if (ajaxRequest.readyState == 4){
	  var ajaxDisplay = document.getElementById('ajaxDiv');
	  ajaxDisplay.innerHTML = ajaxRequest.responseText;
   }
 }

 // Now get the value from user and pass it to
 // server script.
 var trace = document.getElementById('trace').value;
 var trace_id = document.getElementById('trace_id').value;
 var context = document.getElementById('context').value;
 var context_id = document.getElementById('context_id').value;
 var level = document.getElementById('level').value;
 var message = document.getElementById('message').value;
 var queryString = "?trace=" + encodeURIComponent(trace) + "&trace_id=" + encodeURIComponent(trace_id)
	+ "&context=" + encodeURIComponent(context) + "&context_id=" + encodeURIComponent(context_id)
	+ "&level=" + encodeURIComponent(level) + "&message=" + encodeURIComponent(message);

 ajaxRequest.open("GET", "<?php echo Config::Web('/logs/log_table'); ?>" + queryString, true);
 ajaxRequest.send(null);
}
//-->
</script>

<form name='logFilter'>
	<fieldset>
	<legend>Filter Logs</legend>

	<div id="show_hidden" class="show_hidden">
		<a href="javascript:void(0)" id="showfilter" class="showfilter btn"
			onclick="$('#collapse').css('display','block'); $('#show_hidden').css('display','none');return false;">
				<?php echo $this->label("Show Filters"); ?></a>
	</div>

	<div id="collapse" class="collapse">
		<div class="">
			<label for='level'>Level</label>
			<select id='level'>
				<option value="any" selected>Any</option>
				<option value="info">Info</option>
				<option value="warning">Warning</option>
				<option value="error">Error</option>
				<option value="fatal">Fatal</option>
			</select>
		</div>

		<div class="half">
			<label for='trace'>Trace</label>
			<input type='text' id='trace' placeholder="Trace Name"/>
		</div>

		<div class="half omega">
			<label for='trace_id'>&nbsp; </label>
			<input type='text' id='trace_id' placeholder="Unique Trace Id"/>
		</div>
		<br />

		<div class="half">
			<label for='context'>Context</label>
			<input type='text' id='context' placeholder="Context name"/>
		</div>

		<div class="half omega">
			<label for='context_id'>&nbsp; </label>
			<input type='text' id='context_id' placeholder="Unique Context id" />
		</div>
		<br />

		<div class="">
			<label for='message'>Message</label>
			<input type='text' id='message' placeholder="log message content" />
		</div>
	</div>

	<label>&nbsp; </label>
	<input type='button' onclick='ajaxFunction()' value='Query Logs'/>
</fieldset>
</form>

<div id='ajaxDiv'></div>
