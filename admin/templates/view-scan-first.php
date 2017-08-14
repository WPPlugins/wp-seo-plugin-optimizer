<?php
global $wpdb, $wp_roles;

?>
<div class="row">

	<div class="col-md-12 col-xs-12" style="margin-bottom:15px;">
	<div class="custom-box" style="height:100%">
		<h3 class="custom-h3"><i class="fa fa-eye fa-blue" aria-hidden="true"></i>WSPO - Initial Scan</h3>
		
			<div style="max-width:600px; margin:0 auto;">
				<div style="text-align:center; padding:10px 0px;">
				<img class="wspo_logo" src="<?=plugins_url('../img/Signet.png', __FILE__)?>" />
				</div>
			
				<div class="bs-callout bs-callout-danger" style="border-right:solid 1px #ddd; border-top:solid 1px #ddd;border-bottom:solid 1px #ddd; box-shadow:none;">
					<p>Scan your Website and detect unused Plugin-Code on particular Pages and Posts. Deactive unused Plugins and speed up your site! <strong>Start with an initial scan.</strong>
					</p>
					<ul>
						<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please deactivate your Caching-Plugin for a scan</li>
						<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please don´t leave this page during a scan</li>
						<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please check your Website for Errors after adding rules</li>
					</ul>
						 
					</div>
			

				<div id="wspo_scan_auto_types" style="padding:5px; border:solid 1px #ddd; margin-bottom:10px;">
					<form id="scan_auto_type_form" style="margin-top:10px;">
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
									<input style="width:100%;" class="btn wspo_green_submit" type="submit" value="Start Scan">
								</div>
							</div>
						</div>
					</form>
				</div>
			
				
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
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
					<div class="col-sm-12">
					<p><i class="fa fa-info-circle" style="padding-right:5px;" aria-hidden="true"></i><strong>Please don´t leave this page during a scan</strong></p>
					</div>
					<div class="col-md-3">
						<span>Overall Progress (<span id="scan_auto_progress_num">0</span>%)</span>
					</div>
					<div class="col-md-9">
						<progress id="scan_auto_progress" style="width:100%" value="0" max="100"></progress>
					</div>
					
					<div class="col-md-3">
						<span>Current Type (<span id="scan_auto_progress_type_num">0</span>%)</span>
					</div>
					<div class="col-md-9">
						<progress id="scan_auto_progress_type" style="width:100%"  value="0" max="100"></progress>
					</div>
					
					<br/>
					
					<div class="col-md-12">
						<h4 style="margin-top:10px;">Results</h4>
						
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
												Time: <span class="scan-result-variable" data-section="estimated" data-var="total_time_f"></span>
												</td>
												
												<td style="width:30%;">
												Requests: <span class="scan-result-variable" data-section="estimated" data-var="save_request"></span>,
												Size: <span class="scan-result-variable" data-section="estimated" data-var="save_size_f"></span>,
												Time: <span class="scan-result-variable" data-section="estimated" data-var="save_time_f"></span>
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
