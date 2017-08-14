<?php
global $wspo_data;
?>
<div class="row">
	<div class="col-md-12">
	<div class="custom-box">
		<h3 class="custom-h3"><i class="fa fa-cog fa-blue" aria-hidden="true"></i>Settings</h3>
		<form id="wspo_settings_general_save_form" data-nonce="<?=wp_create_nonce('wspo-save-settings')?>">
			<div style="padding:15px; border-bottom:solid 1px #ddd;">
			<div class="row">
				<div class="col-sm-4">
					<label>Update Post Register</label></br>
					<p>Refresh the WSPO Register of your Posts and Pages manually.</p>
				</div>
				<div class="col-sm-8">
				<div>
					<a id="wspo_update_register_btn" class="button btn btn-primary" href="#" data-nonce="<?=wp_create_nonce('wspo-update-cache')?>">Update Post Register</a>
				</div>
				</div>
			</div>
			</div>
			
			<div style="padding:15px; border-bottom:solid 1px #ddd;">
			<div class="row">
				<div class="col-sm-4">
					<label>Activate Dashboard Widget</label></br>
					<p>Deactivating this may solve layout conflicts on the WordPress Dahsboard Panel.</p>
				</div>
				<div class="col-sm-8">
				<div>
					<input type="checkbox" name="activate_dashboard_widget" <?=isset($wspo_data['activate_dashboard_widget']) && $wspo_data['activate_dashboard_widget'] ? 'checked="checked"' : ''?>>
				</div>
				</div>
			</div>
			</div>
			
			<div style="padding:15px; border-bottom:solid 1px #ddd;">
			<div class="row">
			<div class="col-sm-4">
				<label>Ignore Plugins in Scan</label>
			</div>
			<div class="col-sm-8">
			<div>				
				<select name="system_plugins[]" class="selectpicker" multiple>
												<optgroup label="Exclude Plugins">
													<?php
													$system_plugins = explode(',', $wspo_data['system_plugins']);
													foreach ($wspo_plugins as $key => $plugin)
													{
														?>
														<option type="checkbox" value="<?=$key?>" <?=in_array($key, $system_plugins) ? 'selected' : '' ?>> <?=$plugin['Name']?></option>
														<?php
													}
													?>
												</optgroup>
						</select>
				
			</div>
			</div>
			</div>
			</div>

			<?php /*
			<label style="margin-top:15px;">Scan Drafts</label>
			<div>
			<label>
				<input type="checkbox" name="scan_draft" <?=$scan_draft ? 'checked="checked"' : ''?>> Scan Draft Posts
			</label>
			</div> 
			
			<div class="row">
				<div class="col-sm-4">
				<label>Deactivate License</label>
				</div>
				<div class="col-sm-8">
				<button class="button" type="button" data-toggle="modal" data-target="#modal_deactivate_license">Deactivate License</button>
				</div>
			</div>*/ ?>
			
			
			<div style="padding:15px; border-bottom:solid 1px #ddd;">
			<p>Warning: Expirienced Users only!</p>
				<label>
					<input type="checkbox" name="add_bootstrap" <?=isset($wspo_data['add_bootstrap']) && $wspo_data['add_bootstrap'] ? 'checked="checked"' : ''?>>
					Load Bootstrap in Admin Panel
				</label>
				<br/>
				<label>
					<input type="checkbox" name="add_fontawesome" <?=isset($wspo_data['add_fontawesome']) && $wspo_data['add_fontawesome'] ? 'checked="checked"' : ''?>>
					Load Font Awesome in Admin Panel
				</label>
				<br/>
				<label>
					<input type="checkbox" name="add_bootstrap_select" <?=isset($wspo_data['add_bootstrap_select']) && $wspo_data['add_bootstrap_select'] ? 'checked="checked"' : ''?>>
					Load Bootstrap Select in Admin Panel
				</label>
				<br/>
				<label>
					<input type="checkbox" name="add_bootstrap_toggle" <?=isset($wspo_data['add_bootstrap_toggle']) && $wspo_data['add_bootstrap_toggle'] ? 'checked="checked"' : ''?>>
					Load Bootstrap Toggle in Admin Panel
				</label>
				<br/>
				<label>
					<input type="checkbox" name="add_load_chart" <?=isset($wspo_data['add_load_chart']) && $wspo_data['add_load_chart'] ? 'checked="checked"' : ''?>>
					Load Load Chart in Admin Panel
				</label>
				<br/>
				<label>
					<input type="checkbox" name="add_google_chart" <?=isset($wspo_data['add_google_chart']) && $wspo_data['add_google_chart'] ? 'checked="checked"' : ''?>>
					Load Google Chart in Admin Panel
				</label>
			</div>
			
			<input style="margin-top:15px;" class="button" type="submit" value="Save Changes">
		</form>
	</div>
</div>
</div>