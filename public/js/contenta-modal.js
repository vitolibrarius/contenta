
var modal = (function(){
	var method = {};
	var $overlay;
	var $modal;
	var $content;
	var $close;

	// Center the modal in the viewport
	method.center = function () {
		var top, left;

		top = Math.max($(window).height() - $modal.outerHeight(), 0) / 2;
		left = Math.max($(window).width() - $modal.outerWidth(), 0) / 2;

		$modal.css({
			top:top + $(window).scrollTop(),
			left:left + $(window).scrollLeft()
		});
	};

	// Open the modal
	method.open = function (settings) {
		var $heading = $('<h2></h2>').empty().append(settings.heading);
		var $left = $('<div id="modalLeft"></div>').append('<img src="' + settings.img + '" class="icon"></img>');
		var $right = $('<div id="modalRight"></div>').append(
			'<p class="modal_desc">' + settings.description + '</p>' +
			'<p class="modal_confirm">' + settings.confirm + '</p>' +
			'<a class="button" href="' + settings.action + '">' + settings.actionLabel + '</a>'
		);

		$content.empty().append($heading, $left, $right);

		var scrollPosition = [
			self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
			self.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop
		];
		var html = $('html');
		html.data('scroll-position', scrollPosition);
		html.data('previous-overflow', html.css('overflow'));
		html.css('overflow', 'hidden');
		window.scrollTo(scrollPosition[0], scrollPosition[1]);

		$modal.css({
			width: settings.width || 'auto',
			height: settings.height || 'auto'
		});

		method.center();
		$(window).bind('resize.modal', method.center);
		$modal.show();
		$overlay.show();
	};

	// Close the modal
	method.close = function () {
		var html = $('html');
		var scrollPosition = html.data('scroll-position');
		html.css('overflow', html.data('previous-overflow'));
		window.scrollTo(scrollPosition[0], scrollPosition[1]);

		$modal.hide();
		$overlay.hide();
		$content.empty();
		$(window).unbind('resize.modal');
	};

	// Generate the HTML and add it to the document
	$overlay = $('<div id="overlay"></div>');
	$modal = $('<div id="modalDialog"></div>');
	$content = $('<div id="modalContent"></div>');
	$close = $('<a href="#close" title="Close" class="close">X</a>');

	$modal.hide();
	$overlay.hide();
	$modal.append($content, $close);

	$(document).ready(function(){
		$('body').append($overlay, $modal);
	});

	$close.click(function(e){
		e.preventDefault();
		method.close();
	});

	return method;
}());
