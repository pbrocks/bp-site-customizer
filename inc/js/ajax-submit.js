function submit_me3(){
	jQuery.post(brint_ajax_hook.ajaxurl, jQuery("#buildsite").serialize()
		,
		function(response_from_the_action_function){
			jQuery("#response_area").html(response_from_the_action_function);
		}
	);
}
