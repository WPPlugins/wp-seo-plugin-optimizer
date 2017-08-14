
<div>
	<ul class="row nav nav-pills" style="margin:10px 0px;">
		<?php
		$first = true;
		foreach ($wspo_post_types as $post_type)
		{
			?><li style="margin:2px;" <?=$first ? 'class="active"' : ''?>><a style="padding:5px;" data-toggle="tab" href="#wspo_monitoring_page_<?=$post_type->name?>"><?=$post_type->label?></a></li><?php
			$first = false;
		} ?>
	</ul>
	
	<div class="tab-content">
		<?php 
		$first = true;
		foreach ($wspo_post_types as $post_type)
		{
			?>
			<div id="wspo_monitoring_page_<?=$post_type->name?>" class="tab-pane fade <?=$first == true ? 'in active' : ''?>">
				<table class="table table-hover table-scroll">
					<thead>
						<tr>
							<th>Page</th>
							<th>Containing Plugins</th>
							<th style="width:160px;">Efficiency</th>
							<th>Plugin Requests</th>
							<th>Plugin Size</th>
							<!-- <th>Plugin Load Time</th> -->
						</tr>
					</thead>
					<tbody>					
						<?php
							//Post Name, Effizienz, Request, Size, unnecessary Requests, unnecessary Size,
							//Effizienz: (Plugins used / Plugins Total) * 100
							$query = wspo_batch_page_loop($post_type->name, function ($post) {
								?>
								<tr class="wspo-list-item">
									<td>
										<?=get_the_title($post)?>
									</td>
									
									<td>
									
										<div class="wspo_plugin_stats" style="border:none; padding:none;">
											<div style="float:left;" class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-target="template">
												<a href="#" data-toggle="tooltip" title="'<?=get_the_title($post)?>' loads %total_loaded% Plugins, from which %total_used% Plugins are needed.">
													<i class="fa fa-circle item-green" aria-hidden="true"><span>%total_used%</span></i>
												</a>
											</div>
											<div style="float:left;" class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-target="template">
												<a href="#" data-toggle="tooltip" title="'<?=get_the_title($post)?>' loads %total_loaded% Plugins, from which %total_unused% Plugins are unnecessary.">
													<i class="fa fa-circle item-red" aria-hidden="true"><span>%total_unused%</span></i>
												</a>
											</div>
											<button style="float: right; border: none; background: none;" class="btn-show-monitoring-data wspo-lazy-data-interaction-field" href="#" data-target="#modal_show_monitoring_data" data-toggle="modal"><i class="fa fa-eye item-blue"></i></button>

											</br>
										<span class="font-unimportant">Total: <span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_loaded"></span>Plugins</span>

										</div>
										
										
										
										<div class="modal-infos" style="display:none;">
											<div style="text-align: right;
														padding-bottom: 10px;
														margin-bottom: 10px;
														border-bottom: solid 1px #dddddd;
														width: 100%;">
												<span>Legend:</span>
												<a href="#" data-toggle="tooltip" title="Plugins are loaded and used" data-placement="right"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" aria-hidden="true"></i></a></span>
												<a href="#" data-toggle="tooltip" title="Plugins loaded but not used" data-placement="right"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" aria-hidden="true"></i></a></span>
											</div>
								
											<ul class="nav nav-tabs wspo_nav_tabs">
												<li class="active"><a data-toggle="tab" href="#wspo_monitoring_page_used"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" aria-hidden="true"></i>Used Plugins</a></li>
												<li><a data-toggle="tab" href="#wspo_monitoring_page_unused"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" aria-hidden="true"></i>Unused Plugins</a></li>
											</ul>
											
											<div class="tab-content">
												<div data-id="wspo_monitoring_page_used" class="monitoring-data-tab tab-pane fade in active">	
													<label>Pages, this plugin is loaded on and is needed</label>
													<div style="height:200px;overflow-y:scroll;" class="checkbox_container wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="used_plugins" data-target="list">
														<div class="plugin-item item-green">
															<label class="custom-checkboxes">
																<i class="fa fa-circle" aria-hidden="true"></i>
																<span style="color:#444;">%name% (Requests: %request%, Size: %size%, Load Time: %time%)</span>
															</label>
														</div>
													</div>
												</div>
												
												<div data-id="wspo_monitoring_page_unused" class="monitoring-data-tab tab-pane fade">	
													<label>Pages, where the plugin is loaded, but not used</label>
													<div style="height:200px;overflow-y:scroll;" class="checkbox_container wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="unused_plugins" data-target="list">
														<div class="plugin-item item-red">
															<label class="custom-checkboxes">
																<i class="fa fa-circle" aria-hidden="true"></i>
																<span style="color:#444;">%name% (Requests: %request%, Size: %size%, Load Time: %time%)</span>
															</label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</td>
									
									<td style="width:160px;">
											<div class="progress wspo_progress">
												<div class="progress-bar wspo-lazy-data-field item-background-green" data-section="pages" data-key="<?=$post?>" data-attribute="load_eff" data-target="progress_bar_aria" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
													<span class="sr-only">0%</span>
												  </div>
											</div>
											<span class="font-unimportant"><span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="load_eff">0 %</span>%</span>			
									</td>
									
									<td>
										<span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_request"></span> 
										</br> 
										<span class="font-unimportant">CSS: <span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_request_css"></span>, 
										JS: <span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_request_js"></span></span>
									</td>
									
									<td>
										<span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_size"></span></br> 
										<span class="font-unimportant">
										CSS: <span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_size_css"></span>,
										JS: <span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_size_js"></span></span>
									</td>
									
									<?php /*
									<td>
										<span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_time"></span></br> 
										<span class="font-unimportant">CSS: <span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_time_css"></span>,
										JS: <span class="wspo-lazy-data-field" data-section="pages" data-key="<?=$post?>" data-attribute="total_time_js"></span>
									</td>
									*/?>
								</tr>
								<?php
							});
						?>		
					</tbody>
				</table>
			</div>
			
			<?php
			$first = false;
		} ?>
	
	</div>
</div>