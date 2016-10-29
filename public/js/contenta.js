/* ***************
	general functions
 */
var delay = (function(){
	var timer = 0;
	return function(callback, millisec){
		clearTimeout (timer);
		timer = setTimeout(callback, millisec);
	};
})();

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};


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

function refreshAjax(page_url, page_num, data, resultsId) {
	page_num = page_num || "";
	resultsId = resultsId || "ajaxDiv";

	$.ajax({
		type: "GET",
		url: page_url + "/" + page_num,
		data: data,
		dataType: "text",
		success: function(msg){
			var ajaxDisplay = document.getElementById(resultsId);
			ajaxDisplay.innerHTML = msg;
		}
	});
};


$(document).ready(function() {
	/* ***************
		top navigation
	 */
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


	/* ***************
		tabs
	 */
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

	/* ***************
		toggling icons (favorite, wanted)
	 */
	$('body').on('click', 'a.toggle', function (e) {
		var span = $(this).children("span.icon");
		var action = $(this).attr("data-href");
		if (action.length >= 1 ) {
			$.ajax({
				type: "GET",
				url: action,
				dataType: "text",
				data: {
					record_id: $(this).attr('data-recordId')
				},
				success: function(msg) {
					var obj = jQuery.parseJSON( msg );
					var toggle = (obj.toggled_on == true);
					span.toggleClass( "on", toggle );
				}
			});
		}
		return false; // don't follow the link!
	});

	/* ***************
		search results paging
	 */
	$('body').on('click', 'a.page', function (e) {
		var page_num = $(this).attr('data-pagenum');
		var page_url = $(this).attr('data-url');
		var resultsId = $(this).attr('data-resultsId');
		resultsId = resultsId || "ajaxDiv";

		var inputValues = $("form#searchForm").serializeObject();
		console.log( JSON.stringify(inputValues) );

		refreshAjax( page_url, page_num, inputValues, resultsId );
		e.preventDefault();
	});

	/* ***************
		search results paging
	 */

});
