$(document).ready(function() {
	function refreshDaemons() {
		var action = $('#daemons').attr("data-href");
		$.ajax({
			global: false,
			type: "GET",
			url: action,
			success: function(msg){
				if ( msg.count == 0 ) {
					$('#daemons').fadeOut(500).hide();
				}
				else {
					$('#daemons > span').empty();
					$('#daemons > span').html( msg.count.toString() );
					$("#daemons").css("display", "block");
					$('#daemons').fadeIn(1500).show();
				}
			}
		});
	};

	function refreshNotifications() {
		var action = $('#notification_content').attr("data-href");
		var delay_milli = 10000;
		$.ajax({
			global: false,
			type: "GET",
			url: action,
			success: function(msg) {
				$.each(msg.positive, function(key,value) {
					$('<div/>', {
	                    id: 'notification_' + key,
                    	class : 'notification alert alert-info',
                    	text: value
	                }).appendTo('#notification_content')
					.delay(delay_milli)
					.fadeOut();
				});

				$.each(msg.negative, function(key,value) {
					$('<div/>', {
	                    id: 'notification_' + key,
                    	class : 'notification alert alert-error',
                    	text: value
	                }).appendTo('#notification_content')
					.delay(delay_milli)
					.fadeOut();
				});

				$.each(msg.logs, function(key,value) {
					$('<div/>', {
	                    id: 'notification_' + key,
                    	class : 'notification alert alert-' + value.level,
                    	text: value.message
	                }).appendTo('#notification_content')
					.delay(delay_milli)
					.fadeOut();
				});
			}
		});
	};

	refreshDaemons();
	setInterval (function f() {
		refreshDaemons();
	}, 15000);

	refreshNotifications();
	setInterval (function f() {
		refreshNotifications();
	}, 15000);
});
