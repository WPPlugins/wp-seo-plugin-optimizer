<?php
global $wpdb;

$pl_count = count($wspo_plugins);

/*$count_rules = count($wspo_rule_performance);
$total_size_js = 0;
$total_size_css = 0;
$total_requests_js = 0;
$total_requests_css = 0;

foreach ($wspo_rule_performance as $perf)
{
	if ($perf['res'])
	{
		$total_size_js += $perf['total_js_size'];
		$total_size_css += $perf['total_css_size'];
		
		$total_requests_js += $perf['total_js'];
		$total_requests_css += $perf['total_css'];
	}
}*/
?>
<div class="row">
	<div class="col-md-12">
	<div class="custom-box">
		<h3 class="custom-h3"><i class="fa fa-pie-chart fa-blue" aria-hidden="true"></i>Overview</h3>
			<div class="row" style="padding:15px 0;">	
					<div style="display:flex;">
					
					<div class="col-sm-4 col-sm-height">
							<div class="wspo_stats">
							<span class="stats_text">Saved Size (Avg.)</span></br>
							<span class="stats_number wspo-lazy-data-field" data-section="general" data-attribute="avg_save_size"></span> </br>
							Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="total_save_size"></span>	
							</div>	
					</div>
					
					
					<div class="col-sm-4">
							<div class="wspo_stats">
							<span class="stats_text">Saved Requests (Avg.)</span></br>
							<span class="stats_number wspo-lazy-data-field" data-section="general" data-attribute="avg_save_requests"></span> </br>
							Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="total_save_requests"></span>	
							</div>
					</div>
					
					<div class="col-sm-4">
							<div class="wspo_stats">
							<span class="stats_text">Saved Requests Time (Avg.)</span></br>
							<span class="stats_number wspo-lazy-data-field" data-section="general" data-attribute="avg_save_time"></span> </br>
							Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="total_save_time"></span>	
							</div>
					</div>
					
					<div class="col-sm-4">
							<div class="wspo_stats">
							<span class="stats_text">Last Scan</span></br>
							<span class="stats_number"><?=empty($wspo_scans) ? '-' : date("Y-m-d", strtotime($wspo_scans[0]->timestamp));?></span></br>
							<a class="wspo_scan_link" href="#">Scan now!</a>		
							</div>
					</div>
					</div>
				</div>
				
			</div>	
	</div>
	
</div>
				
				<div class="row">
				<div class="col-md-12">
				<div class="custom-box">
				<h3 class="custom-h3"><i class="fa fa-area-chart fa-blue" aria-hidden="true"></i>Plugin Performance
						<a style="margin-left:10px;" href="#" data-html="true" data-toggle="tooltip" title="<strong>View:</strong> Turn the rules off to see original Data of Plugins and Posts. Turn on to see the optimizaton results with active Rules.">
						<input class="wspo-toggle-lazy-type" data-on="With Rules" data-off="Without Rules" style="float:right;" type="checkbox" checked data-toggle="toggle">
						</a>
						</h3>
						
					<div class="row">
					<div class="col-sm-6">
							<div class="row"style="display:flex; padding: 10px 20px 10px 0;">
									<div class="col-sm-4">	
										<div style="position:absolute; left:0px;" class="progress-pie-chart wspo-lazy-data-field" data-section="general" data-attribute="total_page_eff" data-target="pie_chart" data-percent="0"><!--Pie Chart -->
											<div class="ppc-progress">
												<div class="ppc-progress-fill"></div>
											</div>
											<div class="ppc-percents">
											<div class="pcc-percents-wrapper">
												<span>%</span>
											</div>
											</div>
										</div><!--End Chart -->
									</div>	
									<div class="col-sm-8">
										<div style="margin-top:10px;"><span class="stats_text">Avg. Page Efficiency</span>
											<div class="wspo_plugin_stats" style="height:25px; margin-top:10px;">
												<div style="float:left;" class="wspo-lazy-data-field" data-section="general" data-target="template">
													<a href="#" data-toggle="tooltip" title="On Average, a Page loads %avg_page_loaded% Plugins, from which %avg_page_used% Plugins are needed.">
														<i class="fa fa-circle item-green" aria-hidden="true"><span>%avg_page_used%</span></i>
													</a>
												</div>
												<div style="float:left;" class="wspo-lazy-data-field" data-section="general" data-target="template">
													<a href="#" data-toggle="tooltip" title="On Average a Page loads %avg_page_loaded% Plugins, from which %avg_page_unused% Plugins are unnecessary.">
														<i class="fa fa-circle item-red" aria-hidden="true"><span>%avg_page_unused%</span></i>
													</a>
												</div>
											<span class="font-unimportant" style="float:right;">Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="avg_page_loaded"></span>Plugins</span>
											</div>
											
											<div class="wspo_single_plugin" style="margin-top:10px; min-height:100px;">
												<label>Most inefficient Pages</label>
													<ul class="wspo-lazy-data-field" data-section="general" data-attribute="top_ineff_pages" data-target="list" data-noitems="No inefficient Pages found.">
														<li>
														<div class="row">
														<div class="col-sm-6">
														%name% 
														</div>
														<div class="col-sm-6" style="padding:0px;">
														<div class="wspo_progress_wrapper">
														<span class="font-unimportant">%load_eff%%</span>
														<div class="progress wspo_progress">
							
																	  <div class="progress-bar item-background-green" role="progressbar" aria-valuenow="%load_eff%"
																	  aria-valuemin="0" aria-valuemax="100" style="width:%load_eff%%;">
																		<span class="sr-only">%load_eff%</span>
																	  </div>
																	</div>
														</div>
														</div>
														</div>
														</li>
													</ul>
											</div>
										</div>
									</div>
							</div>		
					</div>
					<div class="col-sm-6">
								<div class="row" style="display:flex; padding: 10px 20px 10px 0;">
									<div class="col-sm-4" style="border-left:solid 1px #ddd;;">
										<div style="position:absolute; left:0px;" class="progress-pie-chart wspo-lazy-data-field" data-section="general" data-attribute="total_eff" data-target="pie_chart" data-percent="0"><!--Pie Chart -->
											<div class="ppc-progress">
												<div class="ppc-progress-fill"></div>
											</div>
											<div class="ppc-percents">
											<div class="pcc-percents-wrapper item-green">
												<span>%</span>
											</div>
											</div>
										</div><!--End Chart -->
										
										
									</div>
									<div class="col-sm-8">
											<div style="margin-top:10px;"><span class="stats_text">Avg. Plugin Efficiency</span></div>
											
											<div class="wspo_plugin_stats" style="height:25px; margin-top:10px;">
												<div style="float:left;" class="wspo-lazy-data-field" data-section="general" data-target="template">
													<a href="#" data-toggle="tooltip" title="On Average, a Plugin is loaded on %avg_loaded% Pages, and needed on %avg_used% Pages">
														<i class="fa fa-circle item-green" aria-hidden="true"><span>%avg_used%</span></i>
													</a>
												</div>
												<div style="float:left;" class="wspo-lazy-data-field" data-section="general" data-target="template">
													<a href="#" data-toggle="tooltip" title="On Average, a Plugin is loaded on %avg_loaded% Pages, but unnecessary on %avg_unused% Pages">
														<i class="fa fa-circle item-red" aria-hidden="true"><span>%avg_unused%</span></i>
													</a>
												</div>
											<span class="font-unimportant" style="float:right;">Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="avg_loaded"></span>Pages</span>
											</div>
											
											<div class="wspo_single_plugin" style="margin-top:10px; min-height:100px;">
												<label>Most inefficient Plugins</label>
													<ul class="wspo-lazy-data-field" data-section="general" data-attribute="top_ineff_plugins" data-target="list" data-noitems="No inefficient Plugins found.">
														<li>
														<div class="row">
														<div class="col-sm-6">
														%name% 
														</div>
														<div class="col-sm-6">
														<div class="wspo_progress_wrapper">
														<span class="font-unimportant">%load_eff%%</span>
														<div class="progress wspo_progress">
							
																	  <div class="progress-bar item-background-green" role="progressbar" aria-valuenow="%load_eff%"
																	  aria-valuemin="0" aria-valuemax="100" style="width:%load_eff%%;">
																		<span class="sr-only">%load_eff%</span>
																	  </div>
																	</div>
														</div>
														</div>	
														</div>
														</li>
													</ul>
											</div>
									</div>
								</div>	
					</div>	
					</div>		
				</div>
				</div>
				</div>

							
<div class="row">
	<div style="display:flex;">
	<div class="col-md-6 col-xs-6" style="margin-bottom:15px;">
		<div class="custom-box" style="min-height:400px;">
			<h3 class="custom-h3"><i class="fa fa-list fa-blue" aria-hidden="true"></i>Top Rules</h3>
			<ul class="nav nav-tabs wspo_nav_tabs">
				<li class="active"><a data-toggle="tab" href="#wspo_overview_top_rules_size">By Total Size</a></li>
				<li><a data-toggle="tab" href="#wspo_overview_top_rules_request">By Total Requests</a></li>
			</ul>
			
			<div class="tab-content">
				<div id="wspo_overview_top_rules_size" class="tab-pane fade in active">
					<div class="wspo_table_no_scroll">
						<table class="table table-hover" >
							<thead>
							  <tr>
								<th>Scan name</th>
								<th>Plugins</th>
								<th>Total saved size</th>
							  </tr>
							</thead>	
							<tbody class="wspo-lazy-data-field" data-section="general" data-attribute="top_rules_size" data-target="list">
								<tr>
								<td>%title%</td>
								<td><a href="#" data-toggle="tooltip" data-html="true" data-placement="bottom" title="<b>Plugins</b><br/>%plugin_str%" style="padding-left:5px;padding-right:5px"><i class="fa fa-file"> <span>%plugins%</span></i></a>
								</td>							
								<td>
								%saved%
								<a href="#" data-toggle="tooltip" data-html="true" title="CSS: %saved_css% </br> JS: %saved_js%">
								<i class="fa fa-info-circle custom_tooltip"></i></a>
								
								</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				
				<div id="wspo_overview_top_rules_request" class="tab-pane fade">
					<div class="wspo_table_no_scroll">
						<table class="table table-hover" >
							<thead>
							  <tr>
								<th>Scan name</th>
								<th>Plugins</th>
								<th>Total saved requests</th>
							  </tr>
							</thead>	
							<tbody class="wspo-lazy-data-field" data-section="general" data-attribute="top_rules_requests" data-target="list">
								<tr>
								<td>%title%</td>
								<td><a href="#" data-toggle="tooltip" data-html="true" data-placement="bottom" title="<b>Plugins</b><br/>%plugin_str%" style="padding-left:5px;padding-right:5px"><i class="fa fa-file"> <span>%plugins%</span></i></a>
								</td>							
								<td>
								%saved%
								<a href="#" data-toggle="tooltip" data-html="true" title="CSS: %saved_css% </br> JS: %saved_js%">
								<i class="fa fa-info-circle custom_tooltip"></i></a>
								
								</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-xs-6" style="margin-bottom:15px;">
		<div class="custom-box" style="min-height:400px;">
			<h3 class="custom-h3"><i class="fa fa-eye fa-blue" aria-hidden="true"></i>Top Scans</h3>
			
			<ul class="nav nav-tabs wspo_nav_tabs">
				<li class="active"><a data-toggle="tab" href="#wspo_overview_top_scan_size">By Total Size</a></li>
				<li><a data-toggle="tab" href="#wspo_overview_top_scan_request">By Total Requests</a></li>
			</ul>
			
			<div class="tab-content">
				<div id="wspo_overview_top_scan_size" class="tab-pane fade in active">
					<?php
					$top_size = $wspo_scans;
					usort($top_size, function($a, $b)
					{
						if (!$a->finished || !$b->finished)
							return true;
						
						$est1 = json_decode($a->estimated);
						$est2 = json_decode($b->estimated);
						return ($est1->total_saved_css_size + $est1->total_saved_js_size) < ($est2->total_saved_css_size + $est2->total_saved_js_size);
					});
					?>
					
					<div class="wspo_table_no_scroll">
						<table class="table table-hover" >
							<thead>
							  <tr>
								<th></th>
								<th>Scan name</th>
								<th>Total saved size</th>
							  </tr>
							</thead>	
							<tbody>
					
					
					<?php
						$count = 0;
						foreach ($top_size as $result)
						{
							if ($result->finished)
							{
								$count++;
								if ($count > 5)
									break;
								$est = json_decode($result->estimated);
								?>
								
								<tr class="wspo-list-item">
								<td style="width:40px;">
								<?php
										if ($result->finished)
										{
											?>
											<a href="#" data-toggle="tooltip" title="Scan finished successfully" data-placement="right"><i class="fa fa-check-circle wspo_success wspo_hover"></i></a><?php
										}
										else
										{
											?>
											<a href="#" data-toggle="tooltip" title="Scan aborted" data-placement="right"><i class="fa fa-times-circle wspo_error wspo_hover"></i><?php
										}
										?>
								</td>		
								<td>
								<a href="<?=admin_url('admin.php?page=wspo_plugin_role_main&show=scans&scan_id=' . $result->id)?>">Scan from <?=$result->timestamp?></a>
								</td>
								<td>
								<?=wspo_format_byte($est->total_saved_css_size + $est->total_saved_js_size)?>
								<a href="#" data-toggle="tooltip" data-html="true" title="CSS: <?=wspo_format_byte($est->total_saved_css_size)?> <br/> JS: <?=wspo_format_byte($est->total_saved_js_size)?>"><i class="fa fa-info-circle custom_tooltip"></i></a>
								</td>
								</tr>
								<?php
							}
						}
					?>
					</tbody>
					</table>
					</div>
					
					
				</div>
				<div id="wspo_overview_top_scan_request" class="tab-pane fade">
					<?php
					$top_request = $wspo_scans;
					usort($top_request, function($a, $b)
					{
						if (!$a->finished || !$b->finished)
							return true;
						
						$est1 = json_decode($a->estimated);
						$est2 = json_decode($b->estimated);
						return ($est1->total_saved_css + $est1->total_saved_js) < ($est2->total_saved_css + $est2->total_saved_js);
					});
					?>
					
					<div class="wspo_table_no_scroll">
						<table class="table table-hover" >
							<thead>
							  <tr>
								<th></th>
								<th>Scan name</th>
								<th>Total saved requests</th>
							  </tr>
							</thead>	
							<tbody>
					<?php
						$count = 0;
						foreach ($top_request as $result)
						{
							if ($result->finished)
							{
								$count++;
								if ($count > 5)
									break;
								$est = json_decode($result->estimated);
								?>
								<tr>
								<td style="width:40px;">
								<?php
										if ($result->finished)
										{
											?>
											<a href="#" data-toggle="tooltip" title="Scan finished successfully" data-placement="right"><i class="fa fa-check-circle wspo_success wspo_hover"></i></a><?php
										}
										else
										{
											?>
											<a href="#" data-toggle="tooltip" title="Scan aborted" data-placement="right"><i class="fa fa-times-circle wspo_error wspo_hover"></i><?php
										}
										?>
								</td>	
								<td>
								<a href="<?=admin_url('admin.php?page=wspo_plugin_role_main&scan_id=' . $result->id)?>">Scan from <?=$result->timestamp?></a> 
								</td>
								<td>
								<?=$est->total_saved_css + $est->total_saved_js?> 
								<a href="#" data-toggle="tooltip" data-html="true" title="CSS: <?=$est->total_saved_css?> </br> JS: <?=$est->total_saved_js?>"><i class="fa fa-info-circle custom_tooltip"></i> </a>
								</td>
								</tr>
							<?php	
							}
						}
					?>
					</tbody>
					</table>
					</div>
					
				</div>
			</div>
		</div>	
	</div>
	</div>
</div>
	
<?php /*
	<div class="row">
	<div class="col-md-6">
		<h3>Not scanned Plugins</h3>
		<ul style="height:100px;overflow-y:scroll;">
			<?php
			foreach ($wspo_plugins as $key => $plugin)
			{
				$res = true;
				foreach ($wspo_rules as $r) 
				{
					if (in_array($key, explode(',', $r->plugins)))
					{
						$res = false;
					}
				}
				
				if ($res == true)
				{
					?><li><?=$plugin['Name']?></li><?php
				}
			}
			?>
		</ul>
	</div>
	<div class="col-md-6">
		<h3>Not scanned Plugin-Groups</h3>
		<ul style="height:100px;overflow-y:scroll;">
			<?php
			foreach ($wspo_plugin_groups as $group)
			{
				$res = true;
				foreach ($wspo_rules as $r) 
				{
					if (in_array($group, explode(',', $r->plugins)))
					{
						$res = false;
					}
				}
				
				if ($res == true)
				{
					?><li><?=$group->name?></li><?php
				}
			}
			?>
		</ul>
	</div>
	</div>
	*/ ?>
	<?php /*
<div class="row">
<div class="col-md-12">
	<div class="custom-box">
		<h3 class="custom-h3"><i class="fa fa-plug fa-blue" aria-hidden="true"></i>Currently installed Plugins (<?=$pl_count?>)</h3>
						<div style="float:right; position: absolute; top: 35px; right: 35px;">
						<a href="#" data-toggle="tooltip" title="Turn the rules off to see original Data of Plugins and Posts. Turn on to see the optimizaton results with active Rules.">
						<input class="wspo-toggle-lazy-type" data-on="With Rules" data-off="Without Rules" style="float:right;" type="checkbox" checked data-toggle="toggle">
						</a>
						</div>
			<?php include 'view-plugin-monitoring.php'; ?>
			</div>	
</div>
</div>	
*/ ?>
