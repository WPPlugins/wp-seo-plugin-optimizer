<div class="modal fade" id="modal_delete_scan">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Delete Scan</h4>
      </div>
	  <form id="wspo_delete_scan_form" data-nonce="<?=wp_create_nonce('wspo-delete-scan')?>">
		  <div class="modal-body">
			<input name="scan_id" type="hidden" value="">
			<strong>Do you want to delete the corresponding rule group and rules, too?</strong></br></br>
			
			<input type="checkbox" name="clear_all" checked>
			<label style="font-weight:normal;">Rules, causing the deactivation of plugins on specific pages, will be deleted </label>
		  </div>
		  
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<input class="btn btn-danger" type="submit" value="Delete">
		  </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 