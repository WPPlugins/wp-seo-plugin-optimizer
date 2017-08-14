jQuery(document).ready(function($){
	$('.btn-add-region-rule').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var post = $(this).data('post');
			
			if (post == undefined)
			{
				var $target = $(event.currentTarget),
				$modal = $($target.data('target')),
				region = $target.data('region');
				
				$modal.find('.modal-title').html('Add Rule');
				$modal.find('input[name="region_id"]').val(region);
				$modal.find('input[name="rule_id"]').val('-1');
				
				$modal.find('input[type="checkbox"]').each(function(index){ 
					$(this).prop('checked', false);
				});
				
				$modal.find('select[name="user_role[]"]').selectpicker('deselectAll').selectpicker('val', '-1');
				$modal.find('select[name="plugins[]"]').selectpicker('deselectAll');
				
				$modal.find('.rule-main-context').show();
				$modal.find('.rule-edit-context').hide();
			}
		});
	});
	
	$('.btn-change-region-add-type').each(function(index){
		$(this).click(function(event){
			$('#wspo_add_rule_form').find('input[name="type"]').val($(this).data('type'));
		});
	});
	
	$('.btn-edit-region-rule').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget),
			$modal = $($target.data('target'));
			
			$modal.find('.modal-title').html('Edit Rule');
			$modal.find('input[name="region_id"]').val('-1');
			$modal.find('input[name="rule_id"]').val($target.data('rule'));
			
			$modal.find('select[name="user_role[]"]').selectpicker('val', String($target.data('roles')).split(','));
			$modal.find('select[name="plugins[]"]').selectpicker('val', String($target.data('plugins')).split(','));
			
			$modal.find('.rule-main-context').hide();
			$modal.find('.rule-edit-context').html($target.data('title')).show();
		});
	});
	
	$('#wspo_add_rule_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $target.serialize(),
				action : 'wspo_add_region_rule',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					if (window.location.href.split('/').pop().split('?')[0] == 'post.php')
						location.reload(true);
					else
						window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=rules&region=" + res.region);
				}
				
				if (res.pro == true)
				{
					$('#modal_add_edit_rule').modal('hide');
					$('#modal_pro_rules').modal('show');
				}
			}
		});
	});
	
	$('.select-filter').each(function(index){
		$(this).keypress(function(event){
			if (event.keyCode == '13') {
				event.preventDefault();
            }
		});
		$(this).on('input',function(event){
			var $target = $(event.currentTarget),
			val = $target.val().toLowerCase();
			
			$($target.data('target')).find('input[type="checkbox"]').each(function(index) {
				var $par = $(this).parents('label');
				if ($target.val() == '')
				{
					$par.show();
				}
				else
				{
					if ($par.html().toLowerCase().includes(val))
						$par.show();
					else
						$par.hide();
				}
			});
		});
	});
});