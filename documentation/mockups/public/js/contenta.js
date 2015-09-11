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
