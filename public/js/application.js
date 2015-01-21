function showHideFilter(formId){
	if ( $('#' + formId) ) {
		$('#' + formId).css("display","block");
	}
}

var delay = (function(){
	var timer = 0;
	return function(callback, millisec){
		clearTimeout (timer);
		timer = setTimeout(callback, millisec);
	};
})();


$(function () {
	$('.ajaxLogs').click( function() {
		limit = $(this).attr('limit');
		webdir = $(this).attr('webdir');
		key = $(this).attr('name');
		trace = $(this).attr('trace');
		context_id = $(this).attr('contextid');
		if (key.length >= 1 ) {
			$.ajax({
				type: "GET",
				url: webdir + "/admin/log_inline",
				data: {
					'limit' : limit,
					'context_id' : context_id,
					'trace_id' : key,
					'trace' : trace
				},
				dataType: "text",
				success: function(msg){
					var ajaxDisplay = document.getElementById('ajaxDiv_' + key);
					ajaxDisplay.innerHTML = msg;
				}
			});
		}
		return false; // don't follow the link!
   });
});

$(document).on({
	ajaxStart: function() {
		delay( function(){
			$('.spinner').fadeIn(1000);
		}, 100 );
	},
	ajaxStop: function() {
		delay( function(){
			$('.spinner').fadeOut();
		}, 100 );
	}
});

$(document).ready(function() {
	$('.tab-content:not(:first)').hide();
	$('.tabs li a:not(:first)').addClass('inactive');
	$('.tabs li a').click(function(){		
		var t = $(this).attr('href');
		if($(this).hasClass('inactive')){ //added to not animate when active
			$('.tabs li a').addClass('inactive');		
			$(this).removeClass('inactive');
			$('.tab-content').hide();
			$(t).fadeIn('slow');	
		}			
		return false;
	}) //end click
});
