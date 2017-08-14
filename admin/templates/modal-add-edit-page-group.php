<div class="modal fade" id="modal_add_edit_page_group">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Edit Page Group</h4>
      </div>
	  <form id="wspo_add_page_group_form" data-nonce="<?=wp_create_nonce('wspo-add-page-group')?>">
		  <div class="modal-body">
			<label>Group Name</label>
			<input name="group_id" type="hidden" value="-1">
			<input style="margin-bottom:10px;" class="form-control" name="name" type="text" required>
			
			<label>Select Pages to Group</label>
			<ul class="main-nav row nav nav-pills" style="margin:0px;">
				<li class="dropdown active" style="width:100%">
					<a class="dropdown-toggle btn-change-region-add-type custom_tab_as_select" data-toggle="dropdown" href="#">Pages <span style="float: right; margin-top: 8px;" class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php 
						foreach ($wspo_post_types_all as $post_type)
						{
							?><li><a data-toggle="tab" style="padding:5px;" href="#wspo_add_page_group_page_<?=$post_type->name?>"><?=$post_type->label?></a></li><?php
							$first = false;
						} ?>
					</ul>
				</li>
			</ul>
			
			
			<div class="tab-content">
				<?php 
				$first = true;
				foreach ($wspo_post_types_all as $post_type)
				{
					?>
					<div id="wspo_add_page_group_page_<?=$post_type->name?>" class="checkbox_container tab-pane fade <?=$first == true ? 'in active' : ''?>" style="height:200px;overflow-y:scroll; margin:10px 4px;">
						<div style="margin: 0px 0px 10px 0px;">
							<a class="btn-select" data-select="all" data-target="#wspo_add_page_group_page_<?=$post_type->name?>" href="#">Select All</a>
							<span style="color:#337ab7;"> | </span>
							<a class="btn-select" data-select="none" data-target="#wspo_add_page_group_page_<?=$post_type->name?>" href="#">Deselect All</a>
							
							<div style="float:right">
								<i class="fa fa-search" style="padding-right:5px;"></i><input class="select-filter form-control custom-page-filter" data-target="#wspo_add_page_group_page_<?=$post_type->name?>">
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
					<?php
					$first = false;
				} ?>
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