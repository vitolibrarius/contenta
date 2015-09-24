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
	// top navigation
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

	// tabs
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


$(document).ajaxComplete(function() {
	$('a.pub_wanted.toggle').click(function(e){
		var span = $(this).children("span.icon");
		var action = $(this).attr("data-href");
		if (action.length >= 1 ) {
			$.ajax({
				type: "GET",
				url: action,
				dataType: "text",
				success: function(msg) {
					var obj = jQuery.parseJSON( msg );
					var toggle = (obj.pub_wanted == true);
					span.toggleClass( "on", toggle );
				}
			});
		}
		return false; // don't follow the link!
	});
});
