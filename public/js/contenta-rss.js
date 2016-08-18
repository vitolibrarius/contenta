
$(document).ready(function() {
	$('body').on('click', 'a.nzb', function (e) {
		var ref_guid = "#" + $(this).attr('data-ref_guid');
		$.ajax({
			type: "GET",
			url: NZBDownload_url,
			data: {
				endpoint_id: $(this).attr('data-endpoint_id'),
				name: $(this).attr('data-name'),
				issue: $(this).attr('data-issue'),
				year: $(this).attr('data-year'),
				guid: $(this).attr('data-guid'),
				nzburl: $(this).attr('data-url'),
				postedDate: $(this).attr('data-postedDate')
			},
			dataType: "text",
			success: function(msg){
				var ajaxDisplay = $(ref_guid);
				ajaxDisplay.hide();
				ajaxDisplay.empty().append(msg);
				ajaxDisplay.fadeIn(100).show();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		});
		e.stopPropagation();
		return false;
	});
});
