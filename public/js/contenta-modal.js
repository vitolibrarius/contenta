
var modal = (function(){
	var method = {};
	var $overlay;
	var $modal;
	var $content;
	var $close;
	var $close_action;
	var $action_action;

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
			'<a id="modal_action" class="modal_action button" href="#">' + settings.actionLabel + '</a>'
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

		if (typeof settings.close_action === 'function') {
			$close_action = settings.close_action;
		}

		$action_action = settings.action;
		$("#modal_action").bind("click", (function () {
		    if (typeof $action_action === 'function') {
				$action_action();
				method.close(false);
			}
			else {
	          document.location = $action_action;
			}
			return false;
		}));
	};

	// Close the modal
	method.close = function (runCloseAction) {
		var html = $('html');
		var scrollPosition = html.data('scroll-position');
		html.css('overflow', html.data('previous-overflow'));
		window.scrollTo(scrollPosition[0], scrollPosition[1]);

		if (runCloseAction && typeof $close_action === 'function') {
			$close_action();
			$close_action = null;
		}

		$modal.hide();
		$overlay.hide();
		$content.empty();
		$(window).unbind('resize.modal');
		$('#modal_action').unbind("click");
	};

	// Generate the HTML and add it to the document
	$overlay = $('<div id="overlay"></div>');
	$modal = $('<div id="modalDialog"></div>');
	$content = $('<div id="modalContent"></div>');
	$close = $('<a id="modal_close" href="#close" title="Close" class="close">X</a>');

	$modal.hide();
	$overlay.hide();
	$modal.append($content, $close);

	$(document).ready(function(){
		$('body').append($overlay, $modal);
	});

	$close.click(function(e){
		e.preventDefault();
		method.close(true);
	});

	return method;
}());
