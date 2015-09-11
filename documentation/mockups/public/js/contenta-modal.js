/*
	<div id="openConfirm" class="modalDialog" style="width: auto; height: auto; top: 0px; left: 0px; display: block;">
		<div>
			<a href="#close" title="Close" class="close">X</a>
			<h2>Confirm Delete</h2>
			<div style="width:100%; overflow:hidden;">
				<div style="float:right; width:75%;">
					<p class="modal_desc">This will permanently delete this media  <br>
					<em>Filename.cbz</em></p>
					<p class="modal_confirm">Are you sure you want to continue?</p>
					<a class="button" style="float:right" href="undefined">Delete</a>
				</div>
				<div style="float:left ; width:20%;"><img src="public/img/thumb_01.jpg" class="icon"></div>
			</div>
		</div>
	</div>
*/
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
			'<a class="button" style="float:right" href="' + settings.action + '">' + settings.actionLabel + '</a>'
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
