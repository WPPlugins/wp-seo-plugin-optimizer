<div class="modal fade" id="modal_delete_group">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Delete Group</h4>
      </div>
	  <form id="wspo_delete_group_form" data-nonce="<?=wp_create_nonce('wspo-delete-group')?>">
		  <div class="modal-body">
			<input name="group_id" type="hidden" value="">
			<input name="group" type="hidden" value="">
			<span>Are you sure?</span>
		  </div>
		  
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<input class="btn btn-danger" type="submit" value="Delete">
		  </div>
	  </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 