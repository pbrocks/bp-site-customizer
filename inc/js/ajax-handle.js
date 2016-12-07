jQuery(document).ready(function($) {
	// Perform AJAX build on form submit
	$('#build').on('submit', function(){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_build_object.ajaxurl,
			data: { 
				// 'action': 'the_ajax_hook', //calls wp_ajax_nopriv_ajaxbuild
				'name': $('#build-site-form #name').val(), 
				'domain': $('#build-site-form #domain').val(), 
				'title': $('#build-site-form #title').val() },
				success: function(data){
					$('#response_area').text(data.message);
					if (data.loggedin == true){
						document.location.href = ajax_build_object.redirecturl;
					}
				}
			});
		console.log();
		preventDefault();
	});

});