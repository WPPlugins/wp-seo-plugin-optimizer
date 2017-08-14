<?php
$plugins = explode(',', $scan->plugins);
?>
<div class="modal fade" id="modal_edit_scan_rule">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Edit Scan Rule</h4>
      </div>
	  <form id="wspo_edit_scan_rule" data-nonce="<?=wp_create_nonce('wspo-edit-scan-rule')?>">
		  <div class="modal-body">
			<input name="rule_id" type="hidden" value="-1">
			
			<div style="display:none;">
				<?php
					foreach ($plugins as $plugin)
					{
						$plugin_obj = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
						if ($plugin_obj)
						{
							?>
							<div class="plugin-item item-red">
								<label class="custom-checkboxes">
									<i class="fa fa-circle" aria-hidden="true"></i>
									<input type="checkbox" name="plugins[]" value="<?=$plugin?>">
									<span style="color:#444;"><?=$plugin_obj['Name']?></span>
								</label>
							</div>
							<?php
						}
					}
				?>
			</div>
			<div style="text-align: right;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: solid 1px #dddddd;
    width: 100%;">
				<span>Legend:</span>
				<a href="#" data-toggle="tooltip" title="Plugins loaded and unused (Deactivation recommended)" data-placement="right"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" aria-hidden="true"></i></a></span>
				<a href="#" data-toggle="tooltip" title="Plugins needed on the site" data-placement="right"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" aria-hidden="true"></i></a></span>
				<a href="#" data-toggle="tooltip" title="Plugins don't load files and are therefore not used" data-placement="right"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_warning" aria-hidden="true"></i></a></span>

			</div>
			
			<ul class="nav nav-tabs wspo_nav_tabs">
				<li class="active"><a data-toggle="tab" href="#wspo_scan_rule_yes"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_error" aria-hidden="true"></i>Unused Plugins</a></li>
				<li><a data-toggle="tab" href="#wspo_scan_rule_no"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_success" aria-hidden="true"></i>Needed Plugins</a></li>
				<li><a data-toggle="tab" href="#wspo_scan_rule_maybe"><i class="fa fa-circle wspo_hover wspo_ico_width wspo_warning" aria-hidden="true"></i>Other Plugins</a></li>
			</ul>
			
			<div class="tab-content">
				<div id="wspo_scan_rule_yes" class="tab-pane fade in active">
				
							
						<label>Plugins you should/can deactivate</label>
						<div id="wspo_edit_yes_list" style="height:200px;overflow-y:scroll;" class="checkbox_container">
							<div style="margin: 0px 0px 10px 0px;">
								<a class="btn-select" data-select="all" data-target="#wspo_edit_yes_list" href="#">Select All</a>
								<span> | </span>
								<a class="btn-select" data-select="none" data-target="#wspo_edit_yes_list" href="#">Deselect All</a>
							</div>
						</div>
				
				</div>
				<div id="wspo_scan_rule_no" class="tab-pane fade">
			
							<label>Plugins you shouldn't deactivate</label>
							<div id="wspo_edit_no_list" style="height:200px;overflow-y:scroll;" class="checkbox_container">
								<div style="margin: 0px 0px 10px 0px;">
									<a class="btn-select" data-select="all" data-target="#wspo_edit_no_list" href="#">Select All</a>
									<span> | </span>
									<a class="btn-select" data-select="none" data-target="#wspo_edit_no_list" href="#">Deselect All</a>
								</div>
							</div>
						  </div>
				<div id="wspo_scan_rule_maybe" class="tab-pane fade">
					<label>Other Plugins</label>
							<div id="wspo_edit_maybe_list" style="height:200px;overflow-y:scroll;" class="checkbox_container">
								<div style="margin: 0px 0px 10px 0px;">
									<a class="btn-select" data-select="all" data-target="#wspo_edit_no_list" href="#">Select All</a>
									<span> | </span>
									<a class="btn-select" data-select="none" data-target="#wspo_edit_no_list" href="#">Deselect All</a>
								</div>
					</div>
				
				</div>
				</div>
		</div>
		  
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<input class="btn wspo_green_submit" type="submit" value="Save changes">
		  </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 