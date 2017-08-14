<div class="modal fade" id="modal_delete_region">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Delete Rule Group</h4>
      </div>
	  <form id="wspo_delete_region_form" data-nonce="<?=wp_create_nonce('wspo-delete-plugin-region')?>">
		  <div class="modal-body">
			<input name="region_id" type="hidden" value="">
			<strong>Do you want to move all rules from this scan to the 'Not categorized' Group, rather then deleting them?</strong></br></br>
			
			<input type="checkbox" name="move_rules">
			<label style="font-weight:normal;">Move Rules to 'Uncategorized'</label>
		  </div>
		  
			
		  
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<input class="btn btn-danger" type="submit" value="Delete">
		  </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 