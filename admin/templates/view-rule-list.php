<?php
global $wp_roles, $wpdb;
$rules_t = $rules['type_' . $rule_type];
?>

	<table class="table table-hover table-scroll">
							<thead>
							  <tr>
								<th style="width:60px;">
									<a href="#" data-toggle="tooltip" title="Select Scans to remove. Rules appended to the chosen scans will be deleted." data-placement="right">
										<button id="btn_bulk_delete_region_rule_<?=$region_id?>_<?=$rule_type?>" class="btn btn-danger btn-bulk-delete-rule" data-type="<?=$rule_type?>" data-region="<?=$region_id?>" data-toggle="modal" data-target="#modal_delete_rule" disabled><i class="fa fa-trash" aria-hidden="true"></i></button>
									</a>
								</th>
								<th style="width:200px;">Page/Post</th>
								<th style="width:80px;">Roles</th>
								<th style="width:80px;">Plugins</th>
								<th style="width:200px;">Performance
										<a href="#" data-toggle="tooltip" title="Saved Requests, Size and Time by each rule" data-placement="right">
										<i class="fa fa-info-circle custom_tooltip" style="padding-right:10px;" aria-hidden="true"></i>
										</a>
								</th>
								<th style="width:60px;"></th>
							  </tr>
								<div style="margin: 0px 0px 10px 0px;">
									<a class="btn-select" data-select="all" data-target="#rules_list_wrapper_<?=$region_id?>_<?=$rule_type?>" href="#">Select All</a>
									<span style="color:#337ab7;"> | </span>
									<a class="btn-select" data-select="none" data-target="#rules_list_wrapper_<?=$region_id?>_<?=$rule_type?>" href="#">Deselect All</a>
								</div>
							</thead>
							<tbody id="rules_list_wrapper_<?=$region_id?>_<?=$rule_type?>">
<?php
	if (count($rules_t) == 0)
	{
		?> 
		<tr>
			<td style="width:100%;"><div style="font-weight:bold;">No Rules defined yet...</div></td>
		</tr>	
			<?php
			
	}
	else
	{
		?>
	
			<?php				
		foreach ($rules_t as $rule)
		{
			$plugins = explode(',', $rule->plugins);
			$roles = explode(',', $rule->roles);
			
			?><tr>
					<td style="width:60px;">
						<input class="wspo-rule-bulk-delete" type="checkbox" data-region="<?=$region_id?>" data-bulktarget="#btn_bulk_delete_region_rule_<?=$region_id?>_<?=$rule_type?>" data-type="<?=$rule_type?>" value="<?=$rule->id?>">
					</td>
					<?php
					switch ($rule_type)
					{
						case 0:
							$post = get_post($rule->arg);
							?>
								<td style="width:200px;">
									<label><?=$post->post_title?></label>
								</td>
							<?php
							break;
							
						case 1:
							$args = explode(',', $rule->arg);
							$post_type = get_post_type_object($args[0]);
							?>
								<td style="width:200px;">
									<label><?=$post_type->label?></label>
									<div><?=$args[1] == 'single' ? 'Single' : ($args[1] == 'archive' ? 'Archive' : 'Invalid')?></div>
								</td>
							<?php
							break;
							
						case 2:
							?>
								<td style="width:200px;">
									<label><?=$rule->arg?></label>
								</td>
							<?php
							break;
							
						case 3:
							$group = $wpdb->get_row('SELECT * FROM wspo_page_groups WHERE id = ' . $rule->arg);
							?>
								<td style="width:200px;">
									<label><?=$group->name?></label>
								</td>
							<?php
							break;
					}
					?>
					<td style="width:80px;">
						<?=count($roles)?>
					</td>
					
					<td style="width:80px;">
						<?=count($plugins)?>
					</td>
					
					<td style="width:200px;">
						Requests: <span class="wspo-lazy-data-field" data-section="rules" data-key="<?=$rule->id?>" data-attribute="total_requests"></span>,
						Size: <span class="wspo-lazy-data-field" data-section="rules" data-key="<?=$rule->id?>" data-attribute="total_size"></span>
						<?php /* Time: <span class="wspo-lazy-data-field" data-section="rules" data-key="<?=$rule->id?>" data-attribute="total_time"></span> */ ?>
						<?php
						/*<span class="font-unimportant">No Data available...</span>*/
						?>
					</td>
					
					<td style="width:60px;">
						<?php wspo_get_rule_edit_button($rule, false); ?>
						<a class="btn-delete-region-rule" href="#" data-rule="<?=$rule->id?>" data-toggle="modal" data-target="#modal_delete_rule" ><i class="fa fa-trash"></i></a>
					</td>
				
			</tr><?php
		}  
	}?>
	</tbody>
	</table>
<div style="font-size:9px;">*Added by this plugin. No actual role.</div>
