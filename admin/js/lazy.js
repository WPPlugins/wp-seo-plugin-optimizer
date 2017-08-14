jQuery(document).ready(function($)
{
    $('[data-toggle="tooltip"]').tooltip(); //refresh for dashboard
	$('.wspo-lazy-data-interaction-field').attr('disabled', true);
	$('.wspo-toggle-lazy-type').each(function(index){
		$(this).change(function(event) {
			var $this = $(this);
			if (!$this.attr('disabled') && !$this.hasClass('wspo-loading'))
				wspo_lazy_load($this.prop('checked'));
		})
	});
	
	if (typeof google === 'object')
	{
		google.charts.setOnLoadCallback(function() {
			wspo_lazy_load(true);
		});
	}
	else
	{
		wspo_lazy_load(true);
	}
	
	function wspo_lazy_load(with_rule)
	{
		$('.wspo-toggle-lazy-type').addClass('wspo-loading').attr('disabled', true).prop('checked', with_rule).change();
		$('.wspo-lazy-data-interaction-field').attr('disabled', true);
		$('.wspo-lazy-data-field').each(function(index){
			var $this = $(this);
			
			switch ($this.data('target'))
			{
				case 'pie_chart':
					$this.removeClass('gt-50');
					break;
				
				case 'list':
				case 'template':
					if ($this.data('template') == undefined)
						$this.data('template', $this.html());
					
					$this.html('<i class="fa fa-spinner fa-spin"></i>');
					break;
				
				case 'progress_bar_aria':
					$this.html('<i class="fa fa-spinner fa-spin"></i>');
					break;
					
				case 'google_chart':
				case 'google_bar_chart':
					$this.html('<i class="fa fa-spinner fa-spin fa-6x"></i>');
					break;
					
				default:
					$this.html('<i class="fa fa-spinner fa-spin"></i>');
					break;
			}
		});
		
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				nonce : $('#wspo_lazy_beacon').data('nonce'),
				type : with_rule ? '0' : '1',
				action : 'wspo_lazy_performance_data',
			},
			beforeSend: function()
			{
			},
			success: function(res)
			{
				if (res.success)
				{
					var data = res.data;
					$('.wspo-lazy-data-field').each(function(index){
						var $this = $(this),
						field = undefined;
						
						if ($this.data('section') != undefined && data != undefined)
							field = data[$this.data('section')];
						
						if ($this.data('key') != undefined && field != undefined)
							field = field[$this.data('key')];
						
						if ($this.data('attribute') != undefined && field != undefined)
							field = field[$this.data('attribute')];
						
						if ($this.data('target') == undefined)
						{
							if (field != undefined)
								$this.html(field);
							else
								$this.html('-');
						}
						else
						{
							var no_items = '<p style="padding-left:5px;">No items found.</p>';
							if ($this.data('noitems') != undefined)
								no_items = $this.data('noitems');
							
							switch ($this.data('target'))
							{
								case 'pie_chart':
									if (field != undefined)
									{
										var percent = parseInt(field),
										deg = 360*percent/100;
										
										if (percent > 50)
											$this.addClass('gt-50');
										
										$this.find('.ppc-progress-fill').css('transform','rotate('+ deg +'deg)');
										$this.find('.ppc-percents span').html(percent+'%');
									}
									else
									{
										$this.find('.ppc-progress-fill').css('transform','rotate(0deg)');
										$this.find('.ppc-percents span').html('0%');
									}
									break;
									
								case 'progress_bar_aria':
									if (field != undefined)
									{
										$this.attr('aria-valuenow', field).css('width', field + '%').html('<span class="sr-only">0%</span>');
									}
									else
									{
										$this.attr('aria-valuenow', '0').css('width', '0').html('<span class="sr-only">0%</span>');
									}
									break;
									
								case 'google_chart':
									var chartData = new google.visualization.DataTable();
									chartData.addColumn('string', 'Topping');
									chartData.addColumn('number', 'Slices');
									if (field != undefined)
									{
										chartData.addRows(field);
									}

									var options = {'title':'',
												   'width':400,
												   'height':300,
												   'pieHole': 0.7,
												   'colors':['#36a2eb','#4bc0c0' , '#9966ff', '#ff9f40', '#ffce56', '#c91f43', '#ee1515' , '#248c61', '#09a6b6'],
												   'pieSliceText': 'none'
												   };

									var chart = new google.visualization.PieChart($this.get(0));
									chart.draw(chartData, options);
									break;
									
								case 'google_bar_chart':
									var vals = [];
									vals.push(["Element", "Density", { role: "style" } ]);
									if (field != undefined)
									{
										for (var key in field)
										{
											var c_field = field[key];
											vals.push([c_field[0], parseFloat(c_field[1]['v']), "#337ab7"]);
										}
									}
									var chartData2 = google.visualization.arrayToDataTable(vals);

									var view = new google.visualization.DataView(chartData2);
									view.setColumns([0, 1,
												   { calc: "stringify",
													 sourceColumn: 1,
													 type: "string",
													 role: "annotation" },
												   2]);

									var options2 = {
										title: "",
										width: 500,
										height: 350,
										bar: {groupWidth: "95%"},
										legend: { position: "none" },
									};
									var chart2 = new google.visualization.BarChart($this.get(0));
									chart2.draw(view, options2);
									break;
									
								case 'list':
									if (field != undefined)
									{
										var template = $this.data('template'),
										tags = template.match(/%(.*?)%/g).map(function(val){
										   return val.replace(/%/g,'');
										}),
										result = '';
										
										for (var key in field)
										{
											var c_field = field[key],
											t_res = template;
											
											for (var tag_key in tags)
											{
												var tag = tags[tag_key];
												t_res = t_res.replace('%' + tag + '%', c_field[tag]);
											}
											result += t_res;
										}
										
										if (result == '')
											$this.html(no_items);
										else
											$this.html(result);
									}
									else
									{
										$this.html(no_items);
									}
									break;
									
								case 'template':
									if (field != undefined)
									{
										var template = $this.data('template'),
										tags = template.match(/%(.*?)%/g).map(function(val){
										   return val.replace(/%/g,'');
										}),
										result = '';
										
										for (var tag_key in tags)
										{
											var tag = tags[tag_key];
											template = template.replace('%' + tag + '%', field[tag]);
										}
										
										$this.html(template);
									}
									else
									{
										$this.html('-');
									}
									break;
							}
						}
					});
				}
				
				$('[data-toggle="tooltip"]').tooltip();
				$('.wspo-lazy-data-interaction-field').attr('disabled', false);
				$('.wspo-toggle-lazy-type').attr('disabled', false).change().removeClass('wspo-loading');
			}
		});
	}
});