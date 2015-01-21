
$(document).ready(function() {

	$('#fullsizelink').click(function(e) {
		var i = jQuery.inArray($('#page').attr('src'), images);
		var href = fullsizedURL + i;
	    $('#fullsizelink').attr('href', href);
	});
});
