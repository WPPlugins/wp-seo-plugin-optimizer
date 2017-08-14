jQuery(document).ready(function($)
{
	//Settings (allways first)
	$('#wspo_update_register_btn').click(function(event) {
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				group_id: $(this).data('group'),
				action : 'wspo_update_register',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_settings&res=" + res.success + "&msg=" + encodeURIComponent(res.msg));
				}
			}
		});
	});
	
	$('#wspo_settings_general_save_form').submit(function(event) {
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce : $(this).data('nonce'),
				form_data: $(this).serialize(),
				action : 'wspo_save_settings_general',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_settings&res=" + res.success + "&msg=" + encodeURIComponent(res.msg));
			}
		});
	});
	
	//init
	$('.btn-collapse').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var par = $(this).data('parent');
			
			if (par != undefined)
			{
				$(par).find('.collapse.in').collapse('hide');
			}
		});
	});
	
    $('[data-toggle="tooltip"]').tooltip();
	
	$('.btn-select').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			var target = $(this).data('target');
			var type = $(this).data('select');
			
			if (target != undefined)
			{
				if (type == "all")
				{
					$(target).find('input[type="checkbox"]').each(function(index){
						if (!$(this).is(':disabled'))
							$(this).prop("checked", true);
					});
				}
				
				if (type == "none")
				{
					$(target).find('input[type="checkbox"]').each(function(index){
						if (!$(this).is(':disabled'))
							$(this).prop("checked", false);
					});
				}
			}
		});
	});
	
	//monitoring
	$('.btn-show-monitoring-data').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget),
			$modal = $($target.data('target'));
			$('#modal_show_monitoring_data_target').html('');
			$target.parents('.wspo-list-item').find('.modal-infos').clone().appendTo('#modal_show_monitoring_data_target').show();
			$('#modal_show_monitoring_data_target').find('.monitoring-data-tab').each(function(index){
				$(this).attr('id', $(this).data('id'));
			});
		});
	});
	
	//regions
	$('.wspo-rule-bulk-delete').each(function(index){
		$(this).change(function(){
			var hasCb = false
				$this = $(this),
				region = $this.data('region'),
				type = $(this).data('type');
				
			$('.wspo-rule-bulk-delete').each(function(index){
				var $this = $(this);
				if ($this.prop('checked') && $this.data('region') == region && $this.data('type') == type)
					hasCb = true;
			});
			
			if (!hasCb)
			{
				$($this.data('bulktarget')).prop('disabled', 'disabled');
			}
			else
			{
				$($this.data('bulktarget')).prop('disabled', false);
			}
		});
	});
	
	$('.btn-bulk-delete-rule').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			var ids = [],
				region = $(this).data('region'),
				type = $(this).data('type');
			
			$('.wspo-rule-bulk-delete').each(function(index){
				var $this = $(this);
				
				if ($this.prop('checked') && $this.data('region') == region && $this.data('type') == type)
					ids.push($(this).val());
			});
			
			var $modal = $('#modal_delete_rule');
			$modal.find('input[name="rule_id"]').val(ids.join());
			$modal.find('input[name="move_rules"]').prop('checked', false);
		});
	});
	
	$('.btn-delete-region-rule').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			$target = $(event.currentTarget);
			
			var $modal = $('#modal_delete_rule');
			$modal.find('input[name="rule_id"]').val($target.data('rule'));
			$modal.find('input[name="move_rules"]').prop('checked', false);
		});
	});
	
	$('#wspo_delete_rule_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $(this).serialize(),
				action : 'wspo_delete_plugin_region_rule',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					/*$target.parents('tr').fadeOut('fast', function() {
						$(this).remove();
					});*/
					
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=rules");
				}
			}
		});
	});
	
	$(".wspo_scan_link").each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			$("#custom_view_scan").click();
		});
	});
	
	$('.btn-edit-region').each(function(index){
		$(this).click(function(event){
			var $this = $(this),
			$par = $this.parents('li');
			
			if ($this.hasClass('fa-pencil'))
			{
				event.preventDefault();
				event.stopPropagation();
				$par.find('.region-edit-name').attr('disabled', false);
				$par.find('.region-edit-name-info').show();
				$this.removeClass('fa-pencil').addClass('fa-save');
			}
			else
			{
				var $form = $par.find('.edit-region-form');
				$.ajax({
					url: ajaxurl,
					type: 'post',
					data: {
						nonce: $form.data('nonce'),
						form_data: $form.serialize(),
						action : 'wspo_update_region_name',
					},
					beforeSend: function()
					{
					},
					success: function(res)
					{
					}
				});
				$par.find('.region-edit-name').attr('disabled', 'disabled');
				$par.find('.region-edit-name-info').hide();
				$this.removeClass('fa-save').addClass('fa-pencil');
			}
		});
	});
	
	$('.btn-delete-region').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $modal = $('#modal_delete_region');
			$modal.find('input[name="region_id"]').val($(this).data('region'));
		});
	});
	
	$('#wspo_delete_region_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $target.serialize(),
				action : 'wspo_delete_plugin_region',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=rules");
				}
			}
		});
	});
	
	$('.select-url-post').each(function(index) {
		$(this).change(function(event) {
			var $el = $(this);
			
			if ($el.val() != '-1')
			{
				$el.parents('.add-rule-form').find('.add-url-control').val($el.val());
				$el.val('-1');
			}
		});
	});
	
	//scans	
	$('.wspo-scan-bulk-delete').each(function(index){
		$(this).change(function(){
			var hasCb = false;
			$('.wspo-scan-bulk-delete').each(function(index){
				if ($(this).prop('checked'))
				{
					hasCb = true;
				}
			});
			
			if (!hasCb)
			{
				$('#btn_bulk_delete_scan').prop('disabled', 'disabled');
			}
			else
			{
				$('#btn_bulk_delete_scan').prop('disabled', false);
			}
		});
	});
	
	$('#btn_bulk_delete_scan').click(function(event){
		event.preventDefault();
		var ids = [];
		
		$('.wspo-scan-bulk-delete').each(function(index){
			var $this = $(this);
			
			if ($this.prop('checked'))
				ids.push($(this).val());
		});
		
		var $modal = $('#modal_delete_scan');
		$modal.find('input[name="scan_id"]').val(ids.join());
		$modal.find('input[name="clear_all"]').prop('checked', true);
	});
	
	$('.wspo-delete-scan').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget);
			
			var $modal = $('#modal_delete_scan');
			$modal.find('input[name="scan_id"]').val($target.data('scan'));
			$modal.find('input[name="clear_all"]').prop('checked', true);
		});
	});
	
	$('#wspo_delete_scan_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $(this).serialize(),
				action : 'wspo_delete_scan',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=scans");
				
				/*$target.parents('tr').fadeOut('slow', function() {
					$(this).remove();
				});*/
			}
		});
	});
	
	$('#create_plugin_region_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $(this).serialize(),
				action : 'wspo_create_plugin_region',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=rules");
				}
			}
		});
	});
	
	$('.btn-edit-scan-rule').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget),
			$modal = $($target.data('target')),
			plugins = String($target.data('plugins')).split(','),
			yes_plugins = String($target.data('plugins1')).split(','),
			maybe_plugins = String($target.data('plugins2')).split(','),
			no_plugins = String($target.data('plugins3')).split(',');
			
			$modal.find('input[name="rule_id"]').val($target.data('rule'));
			
			$modal.find('input[name="plugins[]"]').each(function (index) {
				var $this = $(this),
				$par = $this.parents('.plugin-item');
				
				$this.prop('checked', plugins.indexOf($this.val()) != -1 ? true : false);
					
				if (yes_plugins.indexOf($this.val()) != -1)
					$par.appendTo('#wspo_edit_yes_list').removeClass('item-green item-yellow item-red').addClass('item-red');
				
				if (maybe_plugins.indexOf($this.val()) != -1)
					$par.appendTo('#wspo_edit_maybe_list').removeClass('item-green item-yellow item-red').addClass('item-yellow');
				
				if (no_plugins.indexOf($this.val()) != -1)
					$par.appendTo('#wspo_edit_no_list').removeClass('item-green item-yellow item-red').addClass('item-green');
			});
			
			//$modal.find('select[name="user_role[]"]').selectpicker('val', String($target.data('roles')).split(','));
			//$modal.find('select[name="plugins[]"]').selectpicker('val', String($target.data('plugins')).split(','));
		});
	});
	
	$('#wspo_edit_scan_rule').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $(this).serialize(),
				action : 'wspo_edit_scan_rule',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&scan_id=" + res.scan_id);
				}
			}
		});
	});
	
	$('#scan_auto_type_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$('#scan_auto_fail').hide();
		
		window.onbeforeunload = function(){
			 return 'Caution: Scan is still running. Are you sure you want to cancel the scan and leave the site?';
		};
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				type: '0',
				form_data: $(this).serialize(),
				action : 'wspo_start_auto_scan'
			},
			beforeSend: function()
			{
				$target.find('input[type="submit"]').attr('disabled', 'disabled');
			},
			success: function(res)
			{
				if (res.success)
				{					
					$('#scan_auto_scan_view').fadeIn("slow", function() {
						wspo_scan(res.scan_id, res.data, res.type);
					});
				}
				else
				{
					$target.find('input[type="submit"]').attr('disabled', false);
					$('#scan_auto_scan_view').fadeIn("slow", function() {
						$('#scan_auto_fail').show();
					});
				}
			}
		});
	});

	$('#scan_auto_csv_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$('#scan_auto_fail').hide();
		
		var formData = new FormData($(this)[0]);
		formData.append('action', 'wspo_start_auto_scan');
		formData.append('type', '1');
		formData.append('nonce', $(this).data('nonce'));
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function()
			{
				$target.find('input[type="submit"]').attr('disabled', 'disabled');
			},
			success: function(res)
			{
				if (res.success)
				{
					$('#scan_auto_scan_view').fadeIn("slow", function() {
						wspo_scan(res.scan_id, res.data, res.type);
					});
				}
				else
				{
					$target.find('input[type="submit"]').attr('disabled', false);
					$('#scan_auto_scan_view').fadeIn("slow", function() {
						$('#scan_auto_fail').show();
					});
				}
			}
		});
	});
	
	$('#scan_auto_url_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$('#scan_auto_fail').hide();
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				type: '2',
				form_data: $(this).serialize(),
				action : 'wspo_start_auto_scan'
			},
			beforeSend: function()
			{
				$target.find('input[type="submit"]').attr('disabled', 'disabled');
			},
			success: function(res)
			{
				if (res.success)
				{
					$('#scan_auto_scan_view').fadeIn("slow", function() {
						wspo_scan(res.scan_id, res.data, res.type);
					});
				}
				else
				{
					$target.find('input[type="submit"]').attr('disabled', false);
					$('#scan_auto_scan_view').fadeIn("slow", function() {
						$('#scan_auto_fail').show();
					});
				}
			}
		});
	});
	
	$('#wspo_create_scan_region').click(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget),
		execute = [];
		$('input:checked.wspo-execute-rule').each(function(index) {
			execute.push($(this).val());
		});
		
		if (execute.length > 0)
		{
			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					nonce: $(this).data('nonce'),
					scan_id: $target.data('scan'),
					execute: execute.join(),
					action : 'wspo_create_scan_region',
				},
				beforeSend: function()
				{
				},
				success: function(res)
				{
					if (res.success)
					{
						window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&scan_id=" + res.scan_id + "&res=true");
					}
					
					if (res.pro)
					{
						$('#modal_pro_rules_scan').modal('show');
						$('#count_rule_puffer').html(res.left);
					}
				}
			});
		}
	});
	
	function wspo_scan(scan_id, data, type)
	{
		if (type == '0')
		{
			var post_types = data['post_types'];
			for (var i = 0, l = post_types.length; i < l; i++)
			{
				var $nav = $('#scan_auto_result_nav_item').clone().attr('id', '').appendTo('#scan_auto_result_nav').css("display", "");
				var $tab = $('#scan_auto_result_tab_item').clone().attr('id', 'scan_auto_result_item_' + post_types[i]['name']).appendTo('#scan_auto_result_tab').css("display", "");
				
				if (i == 0)
				{
					$nav.addClass('active');
					$tab.addClass('in active');
				}
				
				$nav.find('a').attr('href', '#scan_auto_result_item_' + post_types[i]['name']).html(post_types[i]['label']);
				$tab.remove('#scan_auto_result_list_item');
				
				var $item = $('#scan_auto_task_list_item').clone().attr('id', 'scan_item_' + post_types[i]['name']).appendTo('#scan_auto_task_list').show();
				$item.find('.scan-task-name').html(post_types[i]['label']);
				$item.find('.scan-task-count').html(post_types[i]['count']);
			}
			
			$('#scan_auto_progress').attr('max', data['count_o']).attr('value', '0');
			$('#scan_auto_progress_num').html('0');
			
			wspo_iterate_next(scan_id, data, type, 0, 0);
		}
		else if (type == '1')
		{
			var urls = data['urls'];
			var $nav = $('#scan_auto_result_nav_item').clone().attr('id', '').appendTo('#scan_auto_result_nav').css("display", "").addClass('active');
			var $tab = $('#scan_auto_result_tab_item').clone().attr('id', 'scan_auto_result_item_url').appendTo('#scan_auto_result_tab').css("display", "").addClass('in active');
			
			$nav.find('a').attr('href', '#scan_auto_result_item_url').html('URL');
			$tab.remove('#scan_auto_result_list_item');
			
			var $item = $('#scan_auto_task_list_item').clone().attr('id', 'scan_item_url').appendTo('#scan_auto_task_list').show();
			$item.find('.scan-task-name').html('URL');
			$item.find('.scan-task-count').html(data['count']);
			
			$('#scan_auto_progress').attr('max', data['count_o']).attr('value', '0');
			$('#scan_auto_progress_num').html('0');
			
			wspo_iterate_next(scan_id, data, type, 0, 0);
		}
	}
	
	function wspo_iterate_next(scan_id, data, type, index, value)
	{
		if (type == '0')
		{
			if (index != 0)
			{
				$item = $('#scan_item_' + data['post_types'][index - 1]['name']);
				$item.find('.scan-task-status').removeClass('fa-spinner fa-spin').addClass('fa-check');
			
				if (index >= data['count'])
				{
					wspo_iterate_finish(scan_id);
					return;
				}
			}
			
			$item = $('#scan_item_' + data['post_types'][index]['name']);
			$item.find('.scan-task-status').removeClass('fa-times').addClass('fa-spinner fa-spin');
			
			$('#scan_auto_progress_type').attr('max', data['post_types'][index]['count']).attr('value', '0');
			$('#scan_auto_progress_type_num').html('0');
			
			wspo_iterate_next_id(scan_id, data, index, 0, value);
		}
		else if (type == '1')
		{
			$item = $('#scan_item_url');
			$item.find('.scan-task-status').removeClass('fa-times').addClass('fa-spinner fa-spin');
			wspo_iterate_next_url(scan_id, data, index, value);
		}
	}
	
	function wspo_iterate_next_id(scan_id, data, index, id_index, value)
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $('#wspo_scan_beacon').data('nonce'),
				scan_id: scan_id,
				arg: data['post_types'][index]['ids'][id_index],
				action : 'wspo_auto_scan',
			},
			beforeSend: function() 
			{
			},
			success: function(res)
			{
				$('#scan_auto_progress_type').attr('value', id_index + 1);
				$('#scan_auto_progress_type_num').html(Math.round((id_index + 1) / data['post_types'][index]['count'] * 100));
				
				if (res.success)
				{
					wspo_iterate_result(data['post_types'][index]['name'], res);
				}
				
				if (res.pro == true)
				{
					$('#modal_pro').modal('show');
					wspo_iterate_finish(scan_id);
				}
				else
				{
					value += 1;
					$('#scan_auto_progress').attr('value', value);//index + 1);
					$('#scan_auto_progress_num').html(Math.round(value / data['count_o'] * 100));//(index + 1) / data['count'] * 100));
						
					if (id_index >= data['post_types'][index]['count'] - 1)
					{
						wspo_iterate_next(scan_id, data, '0', index + 1, 0, value);
					}
					else
					{
						wspo_iterate_next_id(scan_id, data, index, id_index + 1, value);
					}
				}
			}
		});
	}
	
	function wspo_iterate_next_url(scan_id, data, index, value)
	{
		if (index >= data['count'])
		{
			$item = $('#scan_item_url');
			$item.find('.scan-task-status').removeClass('fa-spinner fa-spin').addClass('fa-check');
			wspo_iterate_finish(scan_id);
		}
		else
		{
			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					nonce: $('#wspo_scan_beacon').data('nonce'),
					scan_id: scan_id,
					arg: data['urls'][index],
					action : 'wspo_auto_scan',
				},
				beforeSend: function() 
				{
				},
				success: function(res)
				{
					if (res.success)
					{
						wspo_iterate_result('url', res);
					}
					
					if (res.pro == true)
					{
						$('#modal_pro').modal('show');
						wspo_iterate_finish(scan_id);
					}
					else
					{
						value += 1;
						$('#scan_auto_progress').attr('value', value);//index + 1);
						$('#scan_auto_progress_num').html(Math.round(value / data['count_o'] * 100));//(index + 1) / data['count'] * 100));
						
						wspo_iterate_next_url(scan_id, data, index + 1, value);
					}
				}
			});
		}
	}
	
	function wspo_iterate_finish(scan_id)
	{
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $('#wspo_scan_beacon').data('nonce-finish'),
				scan_id: scan_id,
				action : 'wspo_finish_scan',
			},
			beforeSend: function() 
			{
			},
			success: function(res)
			{
				$('#scan_auto_results_link').prop('disabled', false).tooltip('hide')
				  .attr('data-original-title', 'Show generated scan overview')
				  .tooltip('fixTitle')
				  .tooltip('show').click(function (event) {
						window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&scan_id=" + scan_id);
				  });
				window.onbeforeunload = function(){
				};
			}
		});
	}
	
	function wspo_iterate_result(type_name, res)
	{
		if (res.success)
		{
			var $item = $('#scan_auto_result_list_item').clone().attr('id', '').appendTo('#scan_auto_result_item_' + type_name + ' .result-list').show();
			$item.find('.scan-result-title').html(res['title']);
			
			var data = res.data['scan_data'];
			
			$item.find('.scan-result-variable').each(function(index) {
				$(this).html(res.data[$(this).data('section')][$(this).data('var')]);
			});
		}
	}
	
	//groups
	$('.btn-edit-plugin-group').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget),
			$modal = $($target.data('target'));
			
			$modal.find('.modal-title').html('Edit Plugin Group');
			$modal.find('input[name="group_id"]').val($target.data('group'));
			
			var pl = String($target.data('plugins')).split(',');
			
			$modal.find('input[name="name"]').val($target.data('name'));
			$modal.find('input[name="plugins[]"]').each(function(index){ 
				var $this = $(this);
				
				if (pl.indexOf($this.val()) == -1)
					$this.prop('checked', false);
				else
					$this.prop('checked', true);
			});
		});
	});
	
	$('.btn-add-plugin-group').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget),
			$modal = $($target.data('target'));
			
			$modal.find('.modal-title').html('Add Plugin Group');
			$modal.find('input[name="group_id"]').val('-1');
			
			$modal.find('input[name="name"]').val('');
			$modal.find('input[type="checkbox"]').each(function(index){ $(this).prop('checked', false); });
		});
	});
	
	$('#wspo_add_plugin_group_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $target.serialize(),
				action : 'wspo_add_plugin_group',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				//if (res.success)
				//{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=groups");
				//}
			}
		});
	});
	
	$('.btn-edit-page-group').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget),
			$modal = $($target.data('target'));
			
			$modal.find('.modal-title').html('Edit Page Group');
			$modal.find('input[name="group_id"]').val($target.data('group'));
			
			var pl = String($target.data('pages')).split(',');
			
			$modal.find('input[name="name"]').val($target.data('name'));
			$modal.find('input[name="pages[]"]').each(function(index){ 
				var $this = $(this);
				
				if (pl.indexOf($this.val()) == -1)
					$this.prop('checked', false);
				else
					$this.prop('checked', true);
			});
		});
	});
	
	$('.btn-add-page-group').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget),
			$modal = $($target.data('target'));
			
			$modal.find('.modal-title').html('Add Page Group');
			$modal.find('input[name="group_id"]').val('-1');
			
			$modal.find('input[name="name"]').val('');
			$modal.find('input[type="checkbox"]').each(function(index){ $(this).prop('checked', false); });
		});
	});
	
	$('#wspo_add_page_group_form').submit(function(event){
		event.preventDefault();
		
		var $target = $(event.currentTarget);
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $target.serialize(),
				action : 'wspo_add_page_group',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=groups");
				}
				
				if (res.pro)
				{
					$('#modal_add_edit_page_group').modal('hide');
					$('#modal_pro_groups').modal('show');
				}
			}
		});
	});
	
	
	$('.btn-delete-plugin-group').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget);
			
			var $modal = $('#modal_delete_group');
			$modal.find('input[name="group_id"]').val($(this).data('group'));
			$modal.find('input[name="group"]').val('plugin');
		});
	});
	
	$('.btn-delete-page-group').each(function(index){
		$(this).click(function(event){
			event.preventDefault();
			
			var $target = $(event.currentTarget);
			
			var $modal = $('#modal_delete_group');
			$modal.find('input[name="group_id"]').val($(this).data('group'));
			$modal.find('input[name="group"]').val('page');
		});
	});
	
	$('#wspo_delete_group_form').submit(function(event){
		event.preventDefault();
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce: $(this).data('nonce'),
				form_data: $(this).serialize(),
				action : 'wspo_delete_group',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					window.location.href = window.location.href.replace( /[\?#].*|$/, "?page=wspo_plugin_role_main&show=groups");
				}
			}
		});
	});
});