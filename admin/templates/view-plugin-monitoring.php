<table class="table table-hover table-scroll">
	<thead>
		<tr>
			<th>Plugin</th>
			<th>Containing Pages</th>
			<th style="width:160px;">Efficiency</th>
			<th>Avg. Requests</th>
			<th>Avg. Size</th>
			<th>Avg. Requests Time</th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		foreach ($wspo_plugins as $key => $plugin)
		{
			$is_active = is_plugin_active($key);
			?>
			<tr class="wspo-list-item">
				<td>
					<input type="hidden" value="<?=$key?>">
					<span style="font-weight:bold;"><?=$plugin['Name']?></span>
					<?php /* <span style="font-size:10px"><?=$plugin['Version']?></span> */ ?></br>
					<i class="<?=$is_active ? 'fa fa-check-circle' : 'fa fa-times-circle'?>" style="color:<?=$is_active ? '#3ab03a' : '#d9534f'?>;"> <span><?=$is_active ? 'Plugin active' : 'Plugin inactive'?></span></i>
				</td>
				
				<td>				

					<div class="wspo_plugin_stats" style="border:none; padding:none;">
						<div style="float:left;" class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-target="template">
							<a href="#" data-toggle="tooltip" title="<?=$plugin['Name']?> is loaded on %loaded_on% Pages, and used on %used_on% Pages.">
								<i class="fa fa-circle item-green" aria-hidden="true"><span>%used_on%</span></i>
							</a>
						</div>
						<div style="float:left;" class="wspo-lazy-data-field"  data-section="plugins" data-key="<?=$key?>" data-target="template">
							<a href="#" data-toggle="tooltip" title="<?=$plugin['Name']?> is loaded on %loaded_on% Pages, and unused %unused_on% Pages.">
								<i class="fa fa-circle item-red" aria-hidden="true"><span>%unused_on%</span></i>
							</a>
						</div>
						<button style="float: right; border: none; background: none;" class="btn-show-monitoring-data wspo-lazy-data-interaction-field" href="#" data-target="#modal_show_monitoring_data" data-toggle="modal"><i class="fa fa-eye item-blue"></i></button>

						</br>
					<span class="font-unimportant">Total: <span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="loaded_on"></span>Plugins</span>

					
					</div>
					
									<?php //Hidden content to be cloned to the edit modal upon opening(editing) ?>
									<div class="modal-infos" style="display:none;">
										<div style="text-align: right;
													padding-bottom: 10px;
													margin-bottom: 10px;
													border-bottom: solid 1px #dddddd;
													width: 100%;">
											<span>Legend:</span>											
												<a href="#" data-toggle="tooltip" title="Pages the plugin is loaded and used on" data-placement="right"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" aria-hidden="true"></i></a></span>
												<a href="#" data-toggle="tooltip" title="Pages the plugin is loaded on but not used" data-placement="right"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" aria-hidden="true"></i></a></span>
										</div>
							
										<ul class="nav nav-tabs wspo_nav_tabs">
											<li class="active"><a data-toggle="tab" href="#wspo_monitoring_plugin_used"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" aria-hidden="true"></i>Used Pages</a></li>
											<li><a data-toggle="tab" href="#wspo_monitoring_plugin_unused"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" aria-hidden="true"></i>Unused Pages</a></li>
										</ul>
										
										<div class="tab-content">
											<div data-id="wspo_monitoring_plugin_used" class="monitoring-data-tab tab-pane fade in active">	
												<label>Pages, this plugin is loaded on and is needed</label>
												<div style="height:200px;overflow-y:scroll;" class="checkbox_container wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="used_pages" data-target="list">
													<div class="plugin-item item-green">
														<label class="custom-checkboxes">
															<i class="fa fa-circle" aria-hidden="true"></i>
															<span style="color:#444;">%name% (Requests: %request%, Size: %size%, Load Time: %time%)</span>
														</label>
													</div>
												</div>
											</div>
											
											<div data-id="wspo_monitoring_plugin_unused" class="monitoring-data-tab tab-pane fade">	
												<label>Pages, where the plugin is loaded, but not used</label>
												<div style="height:200px;overflow-y:scroll;" class="checkbox_container wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="unused_pages" data-target="list">
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
						  <div class="progress-bar wspo-lazy-data-field item-background-green" data-section="plugins" data-key="<?=$key?>" data-attribute="load_eff" data-target="progress_bar_aria" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
							<span class="sr-only">0%</span>
						  </div>
					</div>
					<span class="font-unimportant"><span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="load_eff">0 %</span>%</span>			


				</td>
				
				<td>
					<div class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-target="template">
						<span>%avg_requests%</span></br>
						<span class="font-unimportant">CSS: <span>%avg_css%</span>, JS: <span>%avg_js%</span></span>
					</div>
				</td>
				
				<td>
					<span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="avg_size"></span> </br>
					<span class="font-unimportant">CSS: <span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="avg_css_size"></span>,
					JS: <span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="avg_js_size"></span></span>
				</td>
				
				<td>
					<span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="avg_time"></span> </br>
					<span class="font-unimportant">CSS: <span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="avg_css_time"></span>,
					JS: <span class="wspo-lazy-data-field" data-section="plugins" data-key="<?=$key?>" data-attribute="avg_js_time"></span></span>
				</td>
			</tr>
			<?php
		}
		?>		
	</tbody>
</table>
		
