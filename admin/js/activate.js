jQuery(document).ready(function($)
{
	$('#wspo_activate_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				form_data: $(this).serialize(),
				action : 'wspo_activate',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main");
				}
				else
				{
					alert(res.result);	
				}
			}
		});
	});
	
	$('#wspo_deactivate_license_form').submit(function(event) {
		event.preventDefault();
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				action : 'wspo_deactivate',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_activate");
				}
				else
				{
					alert(res.result);	
				}
			}
		});
	});
});