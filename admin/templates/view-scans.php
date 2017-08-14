<?php
global $wpdb, $wp_roles;

?>
<div class="row">
<div class="col-sm-12">
	<div class="custom-box bs-callout bs-callout-danger" style="margin-bottom:0px;">
		<h3 class="custom-h3" style="margin-top:0px;"> <i class="fa fa-info-circle" style="padding-right:10px;" aria-hidden="true"></i>Attention</h3>
		<p>Scan your Website and detect unused Plugin-Code on particular Pages and Posts. Deactive unused Plugins and speed up your site!
		</p>
		<ul>
			<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please deactivate your Caching-Plugin for a scan</li>
			<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please don´t leave this page during a scan</li>
			<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please check your Website for Errors after adding rules</li>
		</ul>
			 
		</div>
	</div>	

	<div class="col-md-6 col-xs-6">
	<div class="custom-box" style="min-height:270px">
		<h3 class="custom-h3"><i class="fa fa-eye fa-blue" aria-hidden="true"></i>Start Scan</h3>
		
			<ul class="wspo_nav_tabs nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#wspo_scan_auto_types">Scan by Post Types</a></li>
				<li><a data-toggle="tab" href="#wspo_scan_auto_csv">Upload CSV File</a></li>
				<li><a data-toggle="tab" href="#wspo_scan_auto_url">Quick Scan (URL)</a></li>
			</ul>
		
			
			<div class="tab-content">
				<div id="wspo_scan_auto_types" class="tab-pane fade in active">
					<form id="scan_auto_type_form" style="margin-top:10px;" data-nonce="<?=wp_create_nonce('wspo-start-auto-scan')?>">
						<input type="hidden" name="type" value="0">
						<div>
							<div class="row custom_select_scan">
								<div class="col-md-4">
										<!-- Select Post Type -->
										<label>Select Post Types:</label>
								</div>
								<div class="col-md-8">	
										<select name="post_types[]" class="selectpicker" multiple required>
												<optgroup label="Post Types">
													<?php
													foreach ($wspo_post_types as $post_type)
													{
														?>
														<label>
															<option name="post_types[]" value="<?=$post_type->name?>" selected="selected">
															<?=$post_type->label?></option>
														</label>
														<?php
													}
													?>
												</optgroup>
										</select>
								</div>	
							</div>
									<!-- Select Plugins -->
							<div class="row custom_select_scan">		
								<div class="col-md-4">		
										<label>Select Plugins:</label>
								</div>		
								<div class="col-md-8">		
										<select name="plugins[]" class="selectpicker" multiple required>
												<optgroup label="Post Types">
													<?php
													foreach ($wspo_plugins as $key=>$plugin)
													{
														if (is_plugin_active($key))
														{
															?>
															<label>
																<option name="plugins[]" value="<?=$key?>" selected="selected">
															<?=$plugin['Name']?></option>
															</label>
															<?php
														}
													}
													?>
												</optgroup>
										</select>
								</div>
							</div>
							
							<div class="row custom_select_scan">
								<div class="col-md-4"></div>
								<div class="col-md-8">
									<input style="width:100%;" class="btn wspo_green_submit" type="submit" value="Scan">
								</div>
							</div>
						</div>
					</form>
				</div>
				
				<div id="wspo_scan_auto_csv" class="tab-pane fade">
					<form id="scan_auto_csv_form" style="margin-top:10px;" data-nonce="<?=wp_create_nonce('wspo-start-auto-scan')?>">
						<input type="hidden" name="type" value="1">
						<div>
							<div class="row custom_select_scan">
								<div class="col-md-4">
										<!-- Choose CSV File -->
										<label>Choose CSV File:</label>
								</div>
								<div class="col-md-8">	
									<input class="form-control" style="padding: 5px;" type="file" name="file" accept=".csv" required>
								</div>	
							</div>
									<!-- Select Plugins -->
							<div class="row custom_select_scan">		
								<div class="col-md-4">		
										<label>Select Plugins:</label>
								</div>		
								<div class="col-md-8">		
										<select name="plugins[]" class="selectpicker" multiple required>
												<optgroup label="Post Types">
													<?php
													foreach ($wspo_plugins as $key=>$plugin)
													{
														if (is_plugin_active($key))
														{
															?>
															<label>
																<option name="plugins[]" value="<?=$key?>" selected="selected">
															<?=$plugin['Name']?></option>
															</label>
															<?php
														}
													}
													?>
												</optgroup>
										</select>
								</div>
							</div>
							
							<div class="row custom_select_scan">
								<div class="col-md-4"></div>
								<div class="col-md-8">
									<input style="width:100%;" class="btn wspo_green_submit" type="submit" value="Scan">
								</div>
							</div>
						</div>
					</form>
				</div>
				
				<div id="wspo_scan_auto_url" class="tab-pane fade">
					<form id="scan_auto_url_form" style="margin-top:10px;" data-nonce="<?=wp_create_nonce('wspo-start-auto-scan')?>">
						<input type="hidden" name="type" value="2">
						<div>
							<div class="row custom_select_scan">
								<div class="col-md-4">
										<!-- Select Link -->
										<label>Insert URL to scan:</label>
								</div>
								<div class="col-md-8">	
									<input placeholder="Insert URL" class="form-control" type="text" name="url" required>
								</div>	
							</div>
									<!-- Select Plugins -->
							<div class="row custom_select_scan">		
								<div class="col-md-4">		
										<label>Select Plugins:</label>
								</div>		
								<div class="col-md-8">		
										<select name="plugins[]" class="selectpicker" multiple required>
												<optgroup label="Post Types">
													<?php
													foreach ($wspo_plugins as $key=>$plugin)
													{
														if (is_plugin_active($key))
														{
															?>
															<label>
																<option  name="plugins[]" value="<?=$key?>" selected="selected">
															<?=$plugin['Name']?></option>
															</label>
															<?php
														}
													}
													?>
												</optgroup>
										</select>
								</div>
							</div>
							
							<div class="row custom_select_scan">
								<div class="col-md-4"></div>
								<div class="col-md-8">
									<input style="width:100%;" class="btn wspo_green_submit" type="submit" value="Scan">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="col-md-6 col-xs-6">
		<div class="custom-box pro_col" style="min-height:270px;">
			<h3 class="custom-h3"><i class="fa fa-graduation-cap fa-blue" aria-hidden="true"></i>Be a Pro!</h3>
			<div class="row" style="padding:10px 0;">
			<div class="col-sm-6">
			<ul>
				<li><div class="row"><div class="col-sm-2"><i class="fa fa-check-circle item-green" aria-hidden="true"></i></div><div class="col-sm-10">Add unlimited Plugin-Rules</div></div></li>
				<li><div class="row"><div class="col-sm-2"><i class="fa fa-check-circle item-green" aria-hidden="true"></i></div><div class="col-sm-10">Edit Single Posts</div></div></li>
				<li><div class="row"><div class="col-sm-2"><i class="fa fa-check-circle item-green" aria-hidden="true"></i></div><div class="col-sm-10">Add Rules for User-Roles</div></div></li>
			</ul>
			
			</div>
			<div class="col-sm-6">
				<ul>
				<li><div class="row"><div class="col-sm-2"><i class="fa fa-check-circle item-green" aria-hidden="true"></i></div><div class="col-sm-10">Page Performance Monitoring</div></div></li>
				<li><div class="row"><div class="col-sm-2"><i class="fa fa-check-circle item-green" aria-hidden="true"></i></div><div class="col-sm-10">Premium Support</div></div></li>

				</ul>
			</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div style="width:200px; margin:0 auto;">
					<a style="width:100%; float:right;" class="btn wspo_green_submit" src="http://www.bavoko.services/wspo/">Get WSPO Pro!</a>
					</div>
				</div>
			</div>
	</div>
	</div>
	
	<div id="scan_auto_scan_view" class="col-md-12" style="display:none;">
		<div class="row" style="margin:0px">
		<div class="col-md-12 custom-box">
			<h3 class="custom-h3"><i class="fa fa-spinner fa-blue" aria-hidden="true"></i>Scan running</h3>
			<div id="scan_auto_fail" style="display:none;">Auto Scan couldn't be started...</div>
			
			<div class="col-md-3">
				<h4>Tasks</h4>
				<ul id="scan_auto_task_list">
					<li id="scan_auto_task_list_item" style="display:none;">
						<?php //times, spinner, check?>
						<i class="scan-task-status fa fa-times"></i> <span class="scan-task-name"></span> (<span class="scan-task-count"></span>)
					</li>
				</ul>
			</div>
			
			<div class="col-md-9" style="border-left:solid 1px #ddd;">
				<div class="row">
					<div class="col-md-12">
						<div style="float:right; margin-bottom:10px;">
							<button class="btn btn-primary" id="scan_auto_results_link" data-toggle="tooltip" title="Scan still running" disabled>Show scan results</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
					<p><i class="fa fa-info-circle" style="padding-right:5px;" aria-hidden="true"></i><strong>Please don´t leave this page during a scan</strong></p>

					</div>
					<div class="col-md-3">
						<span>Overall Progress (<span id="scan_auto_progress_num">0</span>%)</span>
						
					</div>
					<div class="col-md-9">
						
						<progress id="scan_auto_progress" style="width:100%" value="0" max="100"></progress>
					</div>
				</div>
				<div class="row">	
					
					<div class="col-md-3">
						<span>Current Type (<span id="scan_auto_progress_type_num">0</span>%)</span>
					</div>
					<div class="col-md-9">
						<progress id="scan_auto_progress_type" style="width:100%"  value="0" max="100"></progress>
					</div>
					
				</div>
				<div class="row">
					
					<div class="col-md-12" style="margin-top:10px;">
						<h4>Results</h4>
						
						<ul id="scan_auto_result_nav" class="nav nav-tabs">
							<li id="scan_auto_result_nav_item" style="display:none;"><a data-toggle="tab" href="#wspo_view_info">Overview</a></li>
						</ul>
						
						<div id="scan_auto_result_tab" class="tab-content" style="padding:10px 0px">
							<div id="scan_auto_result_tab_item" class="tab-pane fade" style="display:none;height:300px;overflow-y:scroll;">
								<div class="wspo_table_no_scroll">
										<table class="result-list table-hover table_scan" style="width: 100%; ">
										<thead>
											<tr>
												<th>Post/Page</th>
												<th>Total Performance (CSS, JS)</th>
												<th>Unnecessary Data (CSS, JS)</th>
											</tr>
										</thead>
										<tbody>
										<tr id="scan_auto_result_list_item" style="display:none;">
												
												<td style="width:30%;"><b class="scan-result-title"></b></td>
												
												<td style="width:30%;">
												Requests: <span class="scan-result-variable" data-section="estimated" data-var="total_request"></span>,
												Size: <span class="scan-result-variable" data-section="estimated" data-var="total_size_f"></span>,
												<!-- Time: <span class="scan-result-variable" data-section="estimated" data-var="total_time_f"></span> -->
												</td>
												
												<td style="width:30%;">
												Requests: <span class="scan-result-variable" data-section="estimated" data-var="save_request"></span>,
												Size: <span class="scan-result-variable" data-section="estimated" data-var="save_size_f"></span>,
												<!-- Time: <span class="scan-result-variable" data-section="estimated" data-var="save_time_f"></span> -->
												</td>
											</tbody>
											</tr>
										</table>	
							</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	
	
	<div class="col-md-12">
		<div id="wspo_scan_bulk_context" class="custom-box">
			<h3 class="custom-h3"><i class="fa fa-area-chart fa-blue" aria-hidden="true"></i>All Scans</h3>
						
						<table class="table table-hover table-scroll scans_list">
							<thead>
							  <tr>
								<th style="width:50px !important;">
									<a href="#" data-toggle="tooltip" title="Select Scans to remove. Rules appended to the chosen scans will be deleted." data-placement="right">
									<button id="btn_bulk_delete_scan" class="btn btn-danger" data-toggle="modal" data-target="#modal_delete_scan" disabled><i class="fa fa-trash" aria-hidden="true"></i></button>
									</a>
									</th>
								<th style="width:50px !important;"></th>
								<th>Name</th>
								<th>Scan Details</th>
								<th>Saved Requests</th>
								<th>Saved Size</th>
								<th style="width:200px !important;">Saved Requests Time</th>
								<th></th>
							  </tr>
								<div style="margin: 0px 0px 10px 0px;">
									<a class="btn-select" data-select="all" data-target="#scans_list_wrapper" href="#">Select All</a>
									<span style="color:#337ab7;"> | </span>
									<a class="btn-select" data-select="none" data-target="#scans_list_wrapper" href="#">Deselect All</a>
								</div>
							</thead>
							
								
							<tbody id="scans_list_wrapper">
							  
						  
					<?php
					foreach ($wspo_scans as $scan)
					{
						$post_types = explode(',', $scan->post_types);
						$plugins = explode(',', $scan->plugins);
						
						if ($scan->type == '0')
							{						
								$type_str = '';
								foreach ($post_types as $type)
								{
									$type = get_post_type_object($type);
									if ($type)
										$type_str .= $type->label . '<br/>';
								}
								
								$plugin_str = '';
								foreach ($plugins as $plugin)
								{
									$pl_path = WP_PLUGIN_DIR . '/' . $plugin;
									if (file_exists($pl_path))
									{
										$plugin = get_plugin_data($pl_path);
										if ($plugin)
											$plugin_str .= $plugin['Name'] . '<br/>';
									}
								}
							}
						
						$estimated = json_decode($scan->estimated);
						?>
						<tr>
								<td style="width:50px !important;">
									<input class="wspo-scan-bulk-delete" type="checkbox" value="<?=$scan->id?>">
								</td>
								
								<td style="text-align:center; width:50px !important;">
									<?php
										if ($scan->finished)
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
									<?php
									if ($scan->finished)
									{
										?><a href="<?=admin_url('admin.php?page=wspo_plugin_role_main&scan_id=' . $scan->id)?>">Scan from <?=$scan->timestamp?></a><?php
									}
									else
									{
										?>Scan from <?=$scan->timestamp?><?php
									}
									?>
								</td>
								
								<td>
									<?php
										if ($scan->type == '0')
										{	?>		
									<a href="#" data-toggle="tooltip" data-html="true" data-placement="bottom" title="<b>Post Types scanned</b><br/><?=$type_str?>" style="padding-left:5px;padding-right:5px"><i class="fa fa-file"> <span><?=count($post_types)?></span></i></a>
									<a href="#" data-toggle="tooltip" data-html="true" data-placement="bottom" title="<b>Plugins scanned</b><br/><?=$plugin_str?>" style="padding-left:5px;padding-right:5px"><i class="fa fa-list-ul"> <span><?=count($plugins)?></span></i></a>
									<?php
									}
									else if ($scan->type == '1')
									{
										?><span>URL Scan</span> <?php
									}
									?>
								
								</td>
								
								<td>
									<?php if ($estimated) { ?>
									<?=$estimated->total_saved_css + $estimated->total_saved_js?>
									<a href="#" data-html="true" data-toggle="tooltip" title="CSS: <?=$estimated->total_saved_css?> </br> JS: <?=$estimated->total_saved_js?> </br> Avg: <?=round($estimated->avg_saved_css + $estimated->avg_saved_js, 2)?> per page"><i class="fa fa-info-circle custom_tooltip"></i></a>
									<?php }?>
								</td>
								
								<td>
									<?php if ($estimated) { ?>
									<?=wspo_format_byte($estimated->total_saved_css_size + $estimated->total_saved_js_size)?>
									<a href="#" data-toggle="tooltip" data-html="true" title="CSS: <?=wspo_format_byte($estimated->total_saved_css_size)?> </br> JS: <?=wspo_format_byte($estimated->total_saved_js_size)?> </br> Avg: <?=wspo_format_byte($estimated->avg_saved_css_size + $estimated->avg_saved_js_size)?> per page"><i class="fa fa-info-circle custom_tooltip"></i></a>
									<?php }?>
								</td>
								
								<td style="width:200px !important;">
									<?php if ($estimated) { ?>
									<?=wspo_format_time($estimated->total_saved_css_time + $estimated->total_saved_js_time)?>
									<a href="#" data-toggle="tooltip" data-html="true" title="CSS: <?=wspo_format_time($estimated->total_saved_css_time)?> </br> JS: <?=wspo_format_time($estimated->total_saved_js_time)?> </br> Avg: <?=wspo_format_time($estimated->avg_saved_css_time + $estimated->avg_saved_js_time)?> per page"><i class="fa fa-info-circle custom_tooltip"></i></a>
									<?php }?>
								</td>
								
								<td>
									<a href="#" class="wspo-delete-scan" data-toggle="modal" data-target="#modal_delete_scan" data-scan="<?=$scan->id?>"><i class="fa fa-trash"></i></a>
								</td>
								</tr>
						<?php
					}
					?>		
							</tbody>
						  </table>
						  
						  
						
			<?php /*			
				<div class="wspo-wrapper">		
				<ul class="wspo-list">
					<?php
					foreach ($wspo_scans as $scan)
					{
						$post_types = explode(',', $scan->post_types);
						$plugins = explode(',', $scan->plugins);
						?>
						<li class="wspo-list-item">
							<div class="row">
								<div class="col-md-1">
									<?php
										if ($scan->finished)
										{
											?>
											<a href="#" data-toggle="tooltip" title="Scan finished successfully" data-placement="right"><i class="fa fa-check-circle wspo_success"></i></a><?php
										}
										else
										{
											?>
											<a href="#" data-toggle="tooltip" title="Scan aborted" data-placement="right"><i class="fa fa-times-circle wspo_error"></i><?php
										}
										?>
								</div>
								
								<div class="col-md-4">
									<?php
									if ($scan->finished)
									{
										?><a href="<?=admin_url('admin.php?page=wspo_plugin_role_main&scan_id=' . $scan->id)?>">Scan from <?=$scan->timestamp?></a><?php
									}
									else
									{
										?>Scan from <?=$scan->timestamp?><?php
									}
									?>
								</div>
								
								<div class="col-md-3">
									<a href="#" data-toggle="tooltip" data-placement="bottom" title="Post Types scanned" style="padding-left:5px;padding-right:5px"><i class="fa fa-file"> <span><?=count($post_types)?></span></i></a>
									<a href="#" data-toggle="tooltip" data-placement="bottom" title="Plugins scanned" style="padding-left:5px;padding-right:5px"><i class="fa fa-list-ul"> <span><?=count($plugins)?></span></i></a>
								</div>
								
								<div class="col-md-3">
									more info...
								</div>
								<div class="col-md-1">
									<a href="#" class="wspo-delete-scan" data-scan="<?=$scan->id?>"><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
			*/ ?>
		</div>
	</div>
</div>