<div class="modal fade" id="modal_add_edit_rule">
  <div class="modal-dialog wspo_modal_add_rule">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Add Rule</h4>
      </div>
	  
	  <form id="wspo_add_rule_form" data-nonce="<?=wp_create_nonce('wspo-add-region-rule')?>">
		<div class="modal-body">
			<input type="hidden" name="rule_id" value="-1">
			<input type="hidden" name="region_id" value="-1">
			<input type="hidden" name="type" value="0">
			
			<label class="rule-edit-context">
			</label>
			<div class="rule-main-context">
				<fieldset class="custom_fieldset">
					<legend class="custom_legend">1. Choose page type</legend>

					<ul class="row nav nav-tabs wspo_nav_tabs" style="margin:0px;">
						<li class="dropdown active">
							<a class="dropdown-toggle btn-change-region-add-type" data-toggle="dropdown" href="#wspo_add_rule_page">Pages <span class="caret"></span></a>
							<ul class="dropdown-menu">
							<?php
								foreach ($wspo_post_types_all as $post_type)
								{
									?><li><a class="btn-change-region-add-type" data-type="0" data-toggle="tab" style="padding:5px;" href="#wspo_add_rule_page_<?=$post_type->name?>"><?=$post_type->label?></a></li><?php
									$first = false;
								} ?>
							</ul>
						</li>
						<li><a class="btn-change-region-add-type" data-type="3" data-toggle="pill" href="#wspo_add_rule_page_groups">Page Groups</a></li>
						<li><a class="btn-change-region-add-type" data-type="2" data-toggle="pill" href="#wspo_add_rule_regex">Link/Regex</a></li>
						
					</ul>
					
					<div class="tab-content">
					<?php /* 
						<div id="wspo_add_rule_page" class="tab-pane fade in active">
							<ul class="main-nav row nav nav-pills" style="margin: 0px;">
								<?php 
								$first = true;
								foreach ($wspo_post_types_all as $post_type)
								{
									?><li <?=$first == true ? 'class="active"' : ''?>><a data-toggle="tab" style="padding:5px;" href="#wspo_add_rule_page_<?=$post_type->name?>"><?=$post_type->label?></a></li><?php
									$first = false;
								} ?>
							</ul>
							
							 above test 

							<div class="tab-content">   */ ?> 

								<?php 
								$first = true;
								foreach ($wspo_post_types_all as $post_type)
								{
									?>
									<div id="wspo_add_rule_page_<?=$post_type->name?>" class="tab-pane fade <?=$first == true ? 'in active' : ''?>">
									<p style="margin-top:10px;">Deactivate plugins on selected Posts or Pages.</p>
									<div style="height:200px;overflow-y:scroll;" class="checkbox_container">
						
										<div style="margin: 0px 0px 10px 0px;">
											<a class="btn-select" data-select="all" data-target="#wspo_add_rule_page_<?=$post_type->name?>" href="#">Select All</a>
											<span style="color:#337ab7;"> | </span>
											<a class="btn-select" data-select="none" data-target="#wspo_add_rule_page_<?=$post_type->name?>" href="#">Deselect All</a>
											
											<div style="float:right">
												<i class="fa fa-search" style="padding-right:5px;"></i><input class="select-filter form-control custom-page-filter" data-target="#wspo_add_rule_page_<?=$post_type->name?>">
											</div>
										</div>
										<?php
										$query = wspo_batch_page_loop($post_type->name, function ($post) {
											?><label class="custom-checkboxes">
												<input type="checkbox" name="pages[]" value="<?=$post?>">
												<?=get_the_title($post)?>
											</label><?php
										});
										?>
									</div>
									</div>
									
									<?php
									$first = false;
								} ?>
								
							<!-- </div> 
						</div>-->
						
						
						<div id="wspo_add_rule_regex" class="tab-pane fade">
							<p style="margin-top:10px;">Deactivate plugins on a known link or regex. (See <a href="http://php.net/manual/de/function.preg-match.php">PHP Regex structure</a>).</p>
							<div class="checkbox_container" style="margin-top:10px; padding:20px">			
								<div class="row">
									<div class="col-sm-4">
										<?php /* <span style="display:table-cell; width:1px"><?=home_url()?>/</span> */ ?>
										<select name="regex_type" class="form-control" style="line-height:34px; height:34px;">
											<option value="0" selected>Perfect match</option>
											<option disabled>Contains (Available in Pro Version)</option>
											<option disabled>Starts with (Available in Pro Version)</option>
											<option disabled>Ends with (Available in Pro Version)</option>
											<option disabled>Regex (Available in Pro Version)</option>
										</select>
									</div>
									<div class="col-sm-8">		
										<input placeholder="Add your Link, Slug or Regex" class="form-control add-url-control" name="regex_url" style="display:table-cell; width:100%">
									</div>
								</div>
								<div class="row" style="margin-top:10px;">
								<div class="col-sm-12">
								<a data-toggle="collapse" data-target="#advanced_regex">Advanced</a>
								
									<div id="advanced_regex" class="collapse" style="margin-top:10px;">
										<label>Set your input in context</label>
										<p>Your input will be in context of the selected area. Default is the entire URL.</br>
										(E. g.: Select 'GET' if you want the rule to be appended only to pages, that have your input in the URL-parameters.)</p>
										<select name="regex_base" class="form-control" style="line-height:34px; height:34px;">
											<option value="0" selected>URL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;("http://www.example.com/site")</option>
											<option disabled>URI &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;("/site/") -- Available in Pro Version</option>
											<option disabled>GET &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;("?var=2") -- Available in Pro Version</option>
										</select>
									</div>
									</div>
									</div>

							</div>
							<br/>
						</div>
						
						<div id="wspo_add_rule_page_groups" class="tab-pane fade">
							<p style="margin-top:10px;">Deactivate plugins on a defined set of pages.<a href="#" data-toggle="tooltip" title="You can create Page Groups in the 'Manage Groups'-Section"><i class="fa fa-info-circle custom_tooltip"></i></a></p>
							<div class="checkbox_container" style="height:200px;overflow-y:scroll;">
								<div style="margin: 0px 0px 10px 0px;">
									<a class="btn-select" data-select="all" data-target="#wspo_add_rule_page_groups" href="#">Select All</a>
									<span style="color:#337ab7;"> | </span>
									<a class="btn-select" data-select="none" data-target="#wspo_add_rule_page_groups" href="#">Deselect All</a>
									
									<div style="float:right">
										<i class="fa fa-search" style="padding-right:5px;"></i><input class="select-filter form-control custom-page-filter" data-target="#wspo_add_rule_page_groups">
									</div>
								</div>
								<?php
								foreach ($wspo_page_groups as $group)
								{
									?><label class="custom-checkboxes">
										<input type="checkbox" name="groups[]" value="<?=$group->id?>">
										<?=$group->name?> (<?=count(explode(',', $group->pages))?> Pages)
									</label><?php
								}
								?>
							</div>
							<br/>
						</div>
					</div>
				</fieldset>
			</div>
			
			
			<div class="row" style="padding:20px 0px; display:flex;">
				<div class="col-md-6" style="display:flex;">
					<fieldset class="custom_fieldset" style="margin-top:10px; width:100%;">
						<legend class="custom_legend">2. Choose User Roles</legend>
					
						<label>The rule will be active for the selected roles</label>
						<br/>
						<select name="user_role[]" class="selectpicker" multiple>
							<optgroup label="System">
								<option disabled>Any* (Available in Pro Version)</option>
								<option disabled>Logged in* (Available in Pro Version)</option>
								<option value="-1">Not logged in*</option>
							</optgroup>
							
							<optgroup label="Roles">
								<option disabled>Available in Pro Version</option>
							</optgroup>
						</select>
						<div style="font-size:9px;">*Added by this plugin. No actual role.</div>
					
					</fieldset>
				</div>
				
				<div class="col-md-6" style="display:flex;">
					<fieldset class="custom_fieldset" style="margin-top:10px; width:100%;">
						<legend class="custom_legend">3. Choose Plugins to deactivate</legend>
				
						<label>Select Plugins</label>
						<br/>
						<select name="plugins[]" class="selectpicker" multiple>
							<optgroup label="Plugins">
								<?php if ($wspo_plugins && !empty($wspo_plugins))
								{
									foreach ($wspo_plugins as $key => $plugin)
									{
										?>
										<option value="<?=$key?>"><?=$plugin['Name']?></option>
										<?php
									}
								}
								else
								{
									?><option disabled>No plugins found</option><?php
								} ?>
							</optgroup>
							<optgroup label="Plugin Groups">
								<?php if ($wspo_plugin_groups && !empty($wspo_plugin_groups))
								{
									foreach ($wspo_plugin_groups as $group)
									{
										?>
										<option value="<?=$group->id?>"><?=$group->name?></option>
										<?php
									}
								}
								else
								{
									?><option disabled>No plugin groups found</option><?php
								} ?>
							</optgroup>
						</select>
					
					</fieldset>
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