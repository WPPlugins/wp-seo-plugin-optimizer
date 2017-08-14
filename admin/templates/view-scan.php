<?php
global $wpdb, $wp_roles;

if ($scan)
{
	$plugins = explode(',', $scan->plugins);
	
	$scan_results = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_scans_auto_data
			WHERE scan_id = " . $wspo_scan
		);
	
	$result_count = count($scan_results);
	
	if ($scan->type == '0')
	{		
		$post_types = explode(',', $scan->post_types);		
		
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
	else if ($scan->type == '1')
	{
		$urls = explode(',', $scan->post_types);
		$url_str = '';
		foreach ($urls as $url)
		{
			$url_str .= $url . '<br/>';
		}
	}
	
	$estimated = json_decode($scan->estimated);
}
?>
<div class="row">
		<div class="col-md-4 col-xs-4" style="margin-bottom:15px;">
			<div class="custom-box" style="min-height:270px;">
				<h3 class="custom-h3"><i class="fa fa-eye fa-blue" aria-hidden="true"></i>Scan Data </h3>
				<div class="row">
					<div class="col-md-12">
					    <ul class="wspo_list">
						
						<li><label>Scan from <?=$scan->timestamp?></labe></li>
					
						<?php
						if ($scan->type == '0')
						{	?>					
							<li>Scanned Post Types: <a href="#" data-toggle="tooltip" data-placement="bottom" data-html="true" title="<b>Post Types scanned</b><br/><?=$type_str?>" style="padding-left:5px;padding-right:5px"><span><?=count($post_types)?></span></a></li>
							<li>Scanned Plugins: <a href="#" data-toggle="tooltip" data-placement="bottom" data-html="true" title="<b>Plugins scanned</b><br/><?=$plugin_str?>" style="padding-left:5px;padding-right:5px"><span><?=count($plugins)?></span></a></li>
							<?php
						}
						else if ($scan->type == '1')
						{	?>					
							<li>Scanned URLs: <a href="#" data-toggle="tooltip" data-placement="bottom" data-html="true" title="<b>Post Types scanned</b><br/><?=$url_str?>" style="padding-left:5px;padding-right:5px"><span><?=count($urls)?></span></a></li>
							<?php
						}
						?>
						
						<?php
						if ($scan->finished)
						{
							?>
							<li><a href="#" data-toggle="tooltip" title="Scan finished successfully"><i class="fa fa-check-circle wspo_success"> <span>Scan finished</span></i></a></li><?php
						}
						else
						{
							?>
							<li><a href="#" data-toggle="tooltip" title="Scan aborted"><i class="fa fa-times-circle wspo_error"> <span>Scan aborted</span></i></a></li><?php
						}
						?>
						<li><a href="#" class="wspo-delete-scan" data-scan="<?=$scan->id?>" data-toggle="modal" data-target="#modal_delete_scan"><i class="fa fa-trash" style="padding-right:5px;"> <span>Delete Scan</span></i></a></li>
						</ul>
						
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-8 col-xs-8" style="margin-bottom:15px;">
			<div class="custom-box" style="min-height:270px;">
				<h3 class="custom-h3"><i class="fa fa-pie-chart fa-blue" aria-hidden="true"></i>Performance Overview</h3>
				
				<div class="row" style="padding:15px 0;">	
					<div class="col-sm-4">
					<div class="wspo_stats">
					<span class="stats_text">Saved Requests <a href="#" data-html="true" data-toggle="tooltip" title="<strong>Saved Requests</strong></br>CSS: <?=$estimated->total_saved_css?> </br> JS: <?=$estimated->total_saved_js?>"><i class="fa fa-info-circle custom_tooltip"></i></a></span>	
					</br>
					<span class="stats_number"><?=$estimated->total_saved_css + $estimated->total_saved_js?></span> </br>
						<span>Avg. Saved Requests: </br>
						<?=round($estimated->avg_saved_css + $estimated->avg_saved_js)?> <a href="#" data-html="true" data-toggle="tooltip" title="CSS: <?=round($estimated->avg_saved_css)?> </br> JS: <?=round($estimated->avg_saved_js)?>"><i class="fa fa-info-circle custom_tooltip"></i></a></span>
					</div>
					</div>
					
					<div class="col-sm-4">
					<div class="wspo_stats">
					<span class="stats_text">Saved Size <a href="#" data-toggle="tooltip" data-html="true" title="<strong>Saved Size</strong></br>CSS: <?=wspo_format_byte($estimated->total_saved_css_size)?> </br> JS: <?=wspo_format_byte($estimated->total_saved_js_size)?>"><i class="fa fa-info-circle custom_tooltip"></i></a></span>	
					</br>
					<span class="stats_number"><?=wspo_format_byte($estimated->total_saved_css_size + $estimated->total_saved_js_size)?></span> </br>
					<span>Avg. Saved Size: </br>
					<?=wspo_format_byte($estimated->avg_saved_css_size + $estimated->avg_saved_js_size)?> <a href="#" data-html="true" data-toggle="tooltip" title="CSS: <?=wspo_format_byte($estimated->avg_saved_css_size)?> </br> JS: <?=wspo_format_byte($estimated->avg_saved_js_size)?>"><i class="fa fa-info-circle custom_tooltip"></i></a></span>
					</div>
					</div>
					
					<div class="col-sm-4">
					<div class="wspo_stats">
					<span class="stats_text">Saved Req. Time <a href="#" data-toggle="tooltip" data-html="true" title="<strong>Saved Requests Time</strong></br>CSS: <?=wspo_format_time($estimated->total_saved_css_time)?> </br> JS: <?=wspo_format_time($estimated->total_saved_js_time)?>"><i class="fa fa-info-circle custom_tooltip"></i></a></span>	
					</br>
					<span class="stats_number"><?=wspo_format_time($estimated->total_saved_css_time + $estimated->total_saved_js_time)?></span> </br>
					<span>Avg. Saved Requests Time: </br>
					<?=wspo_format_time($estimated->avg_saved_css_time + $estimated->avg_saved_js_time)?> <a href="#" data-html="true" data-toggle="tooltip" title="CSS: <?=wspo_format_time($estimated->avg_saved_css_time)?> </br> JS: <?=wspo_format_time($estimated->avg_saved_js_time)?>"><i class="fa fa-info-circle custom_tooltip"></i></a></span>
					</div>
					</div>
					
				</div>	

			</div>
		</div>
		<div class="col-md-12">
			<div class="custom-box" style="padding:15px;">
				<h3 class="custom-h3"><i class="fa fa-file-text fa-blue" aria-hidden="true"></i>Scan Results</h3>
				
				<div>
					<?php 
					if ($scan->type == '0')
					{
						$nav = '';
						$con = '';
						$first = true;
						foreach ($post_types as $type)
						{
							$type = get_post_type_object($type);
							if ($type)
							{
								$nav .= '<li style="margin:2px;" id="scan_result_nav_item_' . $type->name . '" ' . ($first == true ? 'class="active"' : '') .'><a style="padding:5px;" data-toggle="tab" href="#scan_result_tab_' . $type->name . '">' . $type->label . ' (%count_posts%)</a></li>';
								ob_start();
								?>
								<div id="scan_result_tab_<?=$type->name?>" class="tab-pane fade <?=$first == true ? 'in active' : ''?>">
									<div style="margin: 0px 0px 10px 0px;">
										<a class="btn-select" data-select="all" data-target="#scan_result_tab_<?=$type->name?>" href="#">Select All</a>
										<span style="color:#337ab7;"> | </span>
										<a class="btn-select" data-select="none" data-target="#scan_result_tab_<?=$type->name?>" href="#">Deselect All</a>
									</div>
									<table class="table table-hover table-scroll table_scan_results">
											<thead>
											  <tr>
												<th style="width:40px;"></th>
												<th>Name</th>
												<th>Avoidable Requests</th>
												<th>Avoiable Size</th>
												<th style="width:200px;">Avoid. Requests Time</th> 
												<th>Plugin Data</th>
												<th style="width:50px;"></th>
											  </tr>
											</thead>
											
											
											<tbody>
										<?php
										$count_post = 0;
										foreach ($scan_results as $result)
										{
											$args = explode(',', $result->arg);
											$data = json_decode($result->data);
											
											if ($args[1] == $type->name)
											{
												$count_post++;
												$plugins = array();
												$plugins1 = array();
												$plugins2 = array();
												$plugins3 = array();
												$is_edited = false;
												
												foreach ($data->scan_data as $key => $scan_data)
												{
													if ($scan_data->add_rule != $scan_data->needs_rule) //Is not in default configuration
														$is_edited = true;
													
													if ($scan_data->add_rule) //Is marked as add to rule
														array_push($plugins, $key);
														
													if (!$scan_data->used && $scan_data->needs_rule) //Loaded but unused
														array_push($plugins1, $key);
													
													if (!$scan_data->used && !$scan_data->needs_rule) //Unused, because unloaded
														array_push($plugins2, $key);
													
													if ($scan_data->used && !$scan_data->needs_rule) //Used
														array_push($plugins3, $key);
												}
												
												?>
												<tr>
													<td style="width:40px;">
														<input class="wspo-execute-rule" type="checkbox" value="<?=$result->id?>" checked>
													</td>
													<td><label><?=get_the_title($args[0])?></label></td>
													<td>
														<?=$data->estimated->save_css + $data->estimated->save_js?> Files 
													</td>
													<td>
														<?=wspo_format_byte($data->estimated->save_css_size + $data->estimated->save_js_size)?>
												
													</td>
													 <td style="width:200px;">
														<?=wspo_format_time($data->estimated->save_js_time + $data->estimated->save_css_time)?>
													</td>
													 
													
													<td>
															<a href="#" data-toggle="modal" class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" data-toggle="tooltip" title="Plugins loaded and unused (Deactivation recommended)" data-placement="right" aria-hidden="true"></i></a><span style="padding-right:5px;"><?=count($plugins1)?></span>
															<a href="#" data-toggle="modal" class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_warning" data-toggle="tooltip" title="Plugins don't load files and are therefore not used" data-placement="right" aria-hidden="true"></i></a><span style="padding-right:5px;"><?=count($plugins2)?></span>
															<a href="#" data-toggle="modal" class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" data-toggle="tooltip" title="Plugins needed on the site" data-placement="right" aria-hidden="true"></i></a><span style="padding-right:5px;"><?=count($plugins3)?></span>
													</td>
													<?php /*
													<td>
														Is default: <?=!$is_edited ? 'true' : 'false'?><br/>
													</td>
													*/ ?>
													<td style="width:50px;">
														<a href="<?=get_permalink($args[0])?>" target="_blank"><i class="fa fa-link"></i></a>
														<a class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>" href="#"><i class="fa fa-pencil"></i></a>
													</td>
												</tr>
												<?php
											}
										} 
										
										$nav = str_replace('%count_posts%', $count_post, $nav);?>
									</tbody>
									</table>
								</div>
								<?php
								$con .= ob_get_clean();
								$first = false;
							}
						}
						?>
						<ul class="nav nav-pills wspo_nav_tabs">
							<?=$nav?>
						</ul>
						
						<div class="tab-content">
							<?=$con?>
						</div>
						<?php
					}
					else if ($scan->type == '1')
					{
						?>
						<ul class="nav nav-tabs wspo_nav_tabs">
							<li id="scan_result_nav_item_url" class="active"><a data-toggle="tab" href="#scan_result_tab_url">URL</a></li>
						</ul>
						
						<div class="tab-content">
							<div id="scan_result_tab_url" class="tab-pane fade in active">
								<table class="table table-hover table-scroll table_scan_results">
											<thead>
											  <tr>
												<th>Name</th>
												<th>Avoidable Requests</th>
												<th>Avoiable Size</th>
												<th style="width:200px;">Avoid. Requests Time</th> 
												<th>Plugin Data</th>
												<th style="width:50px;"></th>
											  </tr>
											</thead>
											
											
											<tbody>
									<?php
									foreach ($scan_results as $result)
									{
										$data = json_decode($result->data);
										
										$plugins = array();
										$plugins1 = array();
										$plugins2 = array();
										$plugins3 = array();
										$is_edited = false;
										
										foreach ($data->scan_data as $key => $scan_data)
										{
											if ($scan_data->add_rule != $scan_data->needs_rule) //Is not in default configuration
												$is_edited = true;
											
											if ($scan_data->add_rule) //Is marked as add to rule
												array_push($plugins, $key);
												
											if (!$scan_data->used && $scan_data->needs_rule) //Loaded but unused
												array_push($plugins1, $key);
											
											if (!$scan_data->used && !$scan_data->needs_rule) //Unused, because unloaded
												array_push($plugins2, $key);
											
											if ($scan_data->used && !$scan_data->needs_rule) //Used
												array_push($plugins3, $key);
										}
										
										?>
										<tr>
											<td><label><?=$result->arg?></label></td>
											<td>
												<?=$data->estimated->save_css + $data->estimated->save_js?> Files 
											</td>
											<td>
												<?=wspo_format_byte($data->estimated->save_css_size + $data->estimated->save_js_size)?>
										
											</td>
											<td style="width:200px;">
												<?=wspo_format_time($data->estimated->save_js_time + $data->estimated->save_css_time)?>
											</td>
											
											<td>
												<a href="#" data-toggle="modal" class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" data-toggle="tooltip" title="Plugins loaded and unused (Deactivation recommended)" data-placement="right" aria-hidden="true"></i></a><span style="padding-right:5px;"><?=count($plugins1)?></span>
												<a href="#" data-toggle="modal" class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_warning" data-toggle="tooltip" title="Plugins don't load files and are therefore not used" data-placement="right" aria-hidden="true"></i></a><span style="padding-right:5px;"><?=count($plugins2)?></span>
												<a href="#" data-toggle="modal" class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" data-toggle="tooltip" title="Plugins needed on the site" data-placement="right" aria-hidden="true"></i></a><span style="padding-right:5px;"><?=count($plugins3)?></span>
											</td>
											<?php /* <span>
												Is default: <?=!$is_edited ? 'true' : 'false'?><br/>
											</span>
											*/ ?>
											<td style="width:50px;">
												<a href="<?=$result->arg?>" target="_blank"><i class="fa fa-link"></i></a>
												<a class="btn-edit-scan-rule" data-toggle="modal" data-target="#modal_edit_scan_rule" data-rule="<?=$result->id?>" data-plugins="<?=implode(',', $plugins)?>" data-plugins1="<?=implode(',', $plugins1)?>" data-plugins2="<?=implode(',', $plugins2)?>" data-plugins3="<?=implode(',', $plugins3)?>" href="#"><i class="fa fa-pencil"></i></a>
											</td>
										</tr>
										<?php
									} ?>
								</tbody>
									</table>
							</div>
						</div>
						<?php
					} ?>
					
				
					<?php
					$region = $wpdb->get_var( "SELECT id FROM wspo_plugin_regions WHERE id = " . $scan->region_id);
					
					if ($region)
					{
						?><a style="margin-top:10px;" class="btn wspo_green_submit" href="<?=admin_url('admin.php?page=wspo_plugin_role_main&show=rules&region=' . $scan->region_id)?>">View Rules</a><?php
					}
					else
					{
						?>
						<a data-placement="right" href="#" data-toggle="tooltip" title="Turn scan results to rules and deactivate the selected plugins on certain posts.">
						<button style="margin-top:10px;" id="wspo_create_scan_region" data-scan="<?=$scan->id?>" data-nonce="<?=wp_create_nonce('wspo-create-scan-region')?>" class="btn wspo_green_submit">Execute</button>
						</a>
						<?php
					} ?>
					
				</div>
			</div>
	</div>
</div>