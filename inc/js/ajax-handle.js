jQuery(document).ready(function($) {
	// Perform AJAX build on form submit
	$('form#build').on('submit', function(e){
		$('form#build').show().text(ajax_build_object.loadingmessage);
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_build_object.ajaxurl,
			data: { 
				'action': 'the_ajax_hook', //calls wp_ajax_nopriv_ajaxbuild
				'name': $('form#build #name').val(), 
				'domain': $('form#build #domain').val(), 
				'title': $('form#build #title').val() },
				success: function(data){
					$('#response-area').text(data.message);
					if (data.loggedin == true){
						document.location.href = ajax_build_object.redirecturl;
					}
				}
			});
		e.preventDefault();
	});

});