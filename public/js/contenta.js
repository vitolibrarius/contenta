var delay = (function(){
	var timer = 0;
	return function(callback, millisec){
		clearTimeout (timer);
		timer = setTimeout(callback, millisec);
	};
})();

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
	var button = $('#topnav > div');
	var menu = $('#topnav > ul');

	if ( undefined === button )
		return;

	if ( undefined === menu || ! menu.children().length ) {
		button.style.display = 'none';
		return;
	}

	button.click(function(){
		if ( -1 == menu.hasClass( 'menu' ) )
			menu.className = 'menu';

		if ( button.hasClass( 'on' ) ) {
			button.removeClass( 'on' );
			menu.removeClass( 'on' );
		}
		else {
			button.addClass( 'on' );
			menu.addClass('on');
		}
	}) //end click
});
