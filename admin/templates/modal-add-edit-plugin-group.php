<div class="modal fade" id="modal_add_edit_plugin_group">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Edit Rule</h4>
      </div>
	  <form id="wspo_add_plugin_group_form" data-nonce="<?=wp_create_nonce('wspo-add-plugin-group')?>">
		  <div class="modal-body">
			<label>Group Name</label>
			<input name="group_id" type="hidden" value="-1">
			<input style="margin-bottom:10px;" class="form-control" name="name" type="text" required>
			
			<label>Select Plugins to Group</label>
			<div id="wspo_add_group_plugins_list" style="height:200px;overflow-y:scroll;" class="checkbox_container">
				<div style="margin: 0px 0px 10px 0px;">
					<a class="btn-select" data-select="all" data-target="#wspo_add_group_plugins_list" href="#">Select All</a>
					<span> | </span>
					<a class="btn-select" data-select="none" data-target="#wspo_add_group_plugins_list" href="#">Deselect All</a>
				</div>
				<?php
					foreach ($wspo_plugins as $key => $plugin)
					{
						?><label class="custom-checkboxes">
							<input type="checkbox" name="plugins[]" value="<?=$key?>">
							<?=$plugin['Name']?>
						</label><?php
					}
				?>
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