<div class="modal fade" id="modal_delete_rule">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Delete Rule</h4>
      </div>
	  <form id="wspo_delete_rule_form" data-nonce="<?=wp_create_nonce('wspo-delete-plugin-region-rule')?>">
		  <div class="modal-body">
			<input name="rule_id" type="hidden" value="">
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