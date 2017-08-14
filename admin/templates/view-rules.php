<?php
global $wp_roles, $wpdb;
//global $wspo_path, $wspo_regions, $wspo_plugins, $wspo_plugin_groups, $wspo_post_types, $wp_roles;
?>
<div class="row">
	
	<div class="col-md-3">		
		<div class="custom-box" style="padding:10px;">
		<h3 class="custom-h3"><i class="fa fa-tags fa-blue" aria-hidden="true"></i>Rule Groups</h3>
			
				<p>A Rule Group will hold a set of url's and corresponding rules to deactivate the selected plugins and plugin groups.</p>
			
			
			<form id="create_plugin_region_form" data-nonce="<?=wp_create_nonce('wspo-create-plugin-region')?>">
				<div class="row">
					<div class="col-md-8">
						<input class="form-control" name="name" type="text" placeholder="Insert Group name" required>
					</div>
					<div class="col-md-4" style="padding:0px;">
						<input class="btn" type="submit" value="Create">
					</div>
				</div>
			</form>
		
		
		<label style="margin-top:15px; border-top:solid 1px #ddd; padding-top:10px; width:100%;">Current Groups (<?=count($wspo_regions)?>)</label>
			<ul class="nav nav-pills nav-stacked" style="height:500px; overflow-y:scroll; border: solid 1px #dddddd; padding: 5px 2px;">
				<?php
					$rule_count = $wpdb->get_var( "SELECT COUNT(*) FROM wspo_plugin_rules WHERE region_id = -1 OR region_id is NULL");
				?>
				<li <?= isset($_GET['show']) && isset($_GET['region']) && $_GET['region'] != '-1' ? '' : 'class="active"'?> style="padding:0px 2px; margin-bottom:3px;">
					<a data-toggle="pill" href="#rule_region_-1">
						<div class="row">
							<div class="col-md-9">
								<span>Not categorized</span> <br>(<?=$rule_count . ($rule_count == 1 ? ' Rule' : ' Rules')?>)
							</div>
							
							<div class="col-md-3">
							</div>
						</div>
					</a>
				</li>
				<?php
				foreach ($wspo_regions as $region)
				{
					$rule_count = $wpdb->get_var( "SELECT COUNT(*) FROM wspo_plugin_rules WHERE region_id = " . $region->id );
					
					?><li <?= isset($_GET['show']) && isset($_GET['region']) && $_GET['region'] == $region->id ?  'class="active"' : ''?> style="padding:0px 2px; margin-bottom:3px;">
						<a data-toggle="pill" href="#rule_region_<?=$region->id?>">
							<div class="row">
								<div class="col-md-8">
									<form class="edit-region-form" data-nonce="<?=wp_create_nonce('wspo-update-region-name')?>">
										<input type="hidden" name="region_id" value="<?=$region->id?>">
										<input name="name" class="region-edit-name rule_group_name" type="text" value="<?=$region->name?>" disabled> </br>(<?=$rule_count . ($rule_count == 1 ? ' Rule' : ' Rules')?>)
									</form>
									
								</div>
								
								<div class="col-md-4" style="text-align:right;">
									<i class="btn-edit-region fa fa-pencil" data-region="<?=$region->id?>"></i>
									<i class="btn-delete-region fa fa-trash" data-region="<?=$region->id?>" data-toggle="modal" data-target="#modal_delete_region"></i>
									<!--<div style="font-size:11px;"><?=$region->timestamp?></div>-->
								</div>
							</div>
						</a>
					</li><?php
				}
				?>
			</ul>
		</div>
	</div>
	
	<div class="col-md-9">
		<?php /*
		<div class="row custom-box">
			<?php include( $wspo_path. '/templates/view-rule-add.php'); ?>
		</div> */ ?>
		<div class="bs-callout bs-callout-danger">
		<h3 class="custom-h3" style="margin-top:0px;"> <i class="fa fa-info-circle" style="padding-right:10px;" aria-hidden="true"></i>Attention</h3>
		<ul>
			<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please clear your Cache after adding rules, or deactivate your Caching-Plugin</li>
			<li style="list-style-type: disc; margin-left: 20px; margin-bottom:0px; font-size:13px;">Please check your Website for Errors after adding rules</li>
		</ul>
			<a class="custom_alert_link btn-add-region-rule" data-toggle="modal" data-target="#modal_add_edit_rule" data-region="-1">Add a new rule</a> <span style="color:#337ab7;"> | </span>
			<a class="custom_alert_link wspo_scan_link" href="#">Scan your Site</a> 
			 
		</div>
		
		
		<div class="tab-content">
			<?php
			$rules_t = $wpdb->get_results(
				"
				SELECT *
				FROM wspo_plugin_rules
				WHERE region_id = -1 OR region_id is NULL
				"
			);
			
			$rules = array(
				'all' => $rules_t,
				'type_0' => array_filter($rules_t, function($rule) { return $rule->type == 0; }),
				'type_2' => array_filter($rules_t, function($rule) { return $rule->type == 2; }),
				'type_3' => array_filter($rules_t, function($rule) { return $rule->type == 3; }),
			);
			$region_id = '-1';
			?>
			<div id="rule_region_-1" class="rule-region tab-pane fade <?= isset($_GET['show']) && isset($_GET['region']) && $_GET['region'] != '-1' ? '' : 'in active'?>">
				<div class="region-data-wrapper">
					<div class="custom-box">
						<h3 class="custom-h3"><i class="fa fa-list fa-blue" aria-hidden="true"></i>Current Rules for "Not categorized"<a href="#" data-toggle="tooltip" title="This View shows the current Rules for the selected Rule Group. You can add, edit and delete a rule here."><i class="fa fa-info-circle custom_tooltip"></i></a>	
						<button style="margin-left:10px;" class="btn btn-add-region-rule wspo_green_submit" data-toggle="modal" data-target="#modal_add_edit_rule" data-region="-1">
							<i class="fa fa-plus" style="padding-right:5px;"></i>Add Rule
						</button>  
						</h3>
						
						
						<ul class="nav nav-tabs wspo_nav_tabs" style="margin-top:10px;">
						  <li class="active"><a data-toggle="pill" href="#rule_region_add_page_-1">Page (<?=count($rules['type_0'])?>)</a></li>
						  <li><a data-toggle="pill" href="#rule_region_add_page_group-1">Page Group (<?=count($rules['type_3'])?>)</a></li>
						  <li><a data-toggle="pill" href="#rule_region_add_regex_-1">Link/Regex (<?=count($rules['type_2'])?>)</a></li>
						</ul>
						
						<div class="tab-content">
							<div id="rule_region_add_page_-1" class="tab-pane fade in active">
								<?php $rule_type = 0;
								include( $wspo_path. '/templates/view-rule-list.php'); ?>
							</div>
							
							<div id="rule_region_add_regex_-1" class="tab-pane fade">
								<?php $rule_type = 2;
								include( $wspo_path. '/templates/view-rule-list.php'); ?>
							</div>
							
							<div id="rule_region_add_page_group-1" class="tab-pane fade">
								<?php $rule_type = 3;
								include( $wspo_path. '/templates/view-rule-list.php'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			foreach ($wspo_regions as $region)
			{
				$region_id = $region->id;
				$rules_t = $wpdb->get_results(
					"
					SELECT *
					FROM wspo_plugin_rules
					WHERE region_id = " . $region->id . "
					"
				);
				
				$rules = array(
					'all' => $rules_t,
					'type_0' => array_filter($rules_t, function($rule) { return $rule->type == 0; }),
					'type_2' => array_filter($rules_t, function($rule) { return $rule->type == 2; }),
					'type_3' => array_filter($rules_t, function($rule) { return $rule->type == 3; }),
				);
				?>
				<div id="rule_region_<?=$region->id?>" class="rule-region tab-pane fade <?= isset($_GET['show']) && isset($_GET['region']) && $_GET['region'] == $region->id ?  'in active' : ''?>">
					<div class="region-data-wrapper">
						<div class="custom-box">
						<h3 class="custom-h3"><i class="fa fa-list fa-blue" aria-hidden="true"></i>Current Rules for "<?=$region->name?>"<a href="#" data-toggle="tooltip" title="This View shows the current Rules for the selected Rule Group. You can add, edit and delete a rule here."><i class="fa fa-info-circle custom_tooltip"></i></a>
						<button style="margin-left:10px;" class="btn btn-add-region-rule wspo_green_submit" data-toggle="modal" data-target="#modal_add_edit_rule" data-region="<?=$region->id?>">
							<i class="fa fa-plus" style="padding-right:5px;"></i>Add Rule
						</button>  
						</h3>
						
						
							<ul class="nav nav nav-tabs wspo_nav_tabs" style="margin-top:10px;">
							  <li class="active"><a data-toggle="pill" href="#rule_region_add_page_<?=$region->id?>">Page (<?=count($rules['type_0'])?>)</a></li>
							  <li><a data-toggle="pill" href="#rule_region_add_group_<?=$region->id?>">Page Group (<?=count($rules['type_3'])?>)</a></li>
							  <li><a data-toggle="pill" href="#rule_region_add_regex_<?=$region->id?>">Link/Regex (<?=count($rules['type_2'])?>)</a></li>
							</ul>
							
							<div class="tab-content">
								<div id="rule_region_add_page_<?=$region->id?>" class="tab-pane fade in active">
									<?php $rule_type = 0;
									include( $wspo_path. '/templates/view-rule-list.php'); ?>
								</div>
								
								<div id="rule_region_add_regex_<?=$region->id?>" class="tab-pane fade">
									<?php $rule_type = 2;
									include( $wspo_path. '/templates/view-rule-list.php'); ?>
								</div>
								
								<div id="rule_region_add_group_<?=$region->id?>" class="tab-pane fade">
									<?php $rule_type = 3;
									include( $wspo_path. '/templates/view-rule-list.php'); ?>
								</div>
							</div>
						</div>
					</div>
				</div><?php
			}
			?>
		</div>
	</div>
	
	

</div>