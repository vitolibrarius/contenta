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


var modal = (function(){
	var method = {};
	var $panel = $('<div id="openConfirm" class="modalDialog"></div>');
	var $wrapper = $('<div></div>');
	var $close = $('<a href="#close" title="Close" class="close">X</a>');
	var $heading = $('<h2>Test</h2>');
	var $panel_body = $('<div style="width:100%; overflow:hidden;"></div>');
	var $left = $('<div style="float:left ; width:20%;"></div>');
	var $right = $('<div style="float:right; width:75%;"></div>');

	// Center the modal in the viewport
	method.center = function () {
		var top, left;

		top = Math.max($(window).height() - $panel.outerHeight(), 0) / 2;
		left = Math.max($(window).width() - $panel.outerWidth(), 0) / 2;

		$panel.css({
			top:top,
			left:left
		});
	};

	// Open the modal
	method.open = function (settings) {
		$heading.empty().append(settings.heading);
		$left.empty().append('<img src="' + settings.img + '" class="icon"></img>');
		$right.empty().append(
			'<p class="modal_desc">' + settings.description + '</p>' +
			'<p class="modal_confirm">' + settings.confirm + '</p>' +
			'<a class="btn" style="float:right" href="' + settings.action + '">' + settings.actionLabel + '</a>'
		);

		$panel.css({
			width: settings.width || 'auto',
			height: settings.height || 'auto'
		});

		method.center();
		$(window).bind('resize.panel', method.center);
		$panel.show();
	};

	// Close the modal
	method.close = function () {
		$panel.hide();
		$heading.empty();
		$left.empty();
		$right.empty();
		$(window).unbind('resize.panel');
	};

	// Generate the HTML and add it to the document
	$panel.hide();
	$panel.append($wrapper);
	$wrapper.append($close, $heading, $panel_body);
	$panel_body.append($right, $left);

	$(document).ready(function(){
		$('body').append($panel);
	});

	$close.click(function(e){
		e.preventDefault();
		method.close();
	});

	return method;
}());
