
function showHideFilter(formId){
	if ( $('#' + formId) ) {
		$('#' + formId).css("display","block");
	}
}

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
