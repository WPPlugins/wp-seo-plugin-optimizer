<?php
?>
<div class="row">
	<div class="col-md-6">
		<div class="custom-box">
		<h3 class="custom-h3"><i class="fa fa-plug fa-blue" aria-hidden="true"></i>Plugin Groups</h3>
			<button style="position: absolute; right: 35px; top: 35px;" class="btn btn-add-plugin-group" data-toggle="modal" data-target="#modal_add_edit_plugin_group">
				<i class="fa fa-plus" style="padding-right:5px;"></i>Add Group
			</button>  
		<fieldset class="custom_fieldset" style="margin-top:15px;">
		<legend class="custom_legend">Current Rule Groups</legend>
		<ul>
		<?php
		if (count($wspo_plugin_groups) > 0) {
		
			foreach($wspo_plugin_groups as $group)
			{
				?>
				<li style="border-bottom:solid 1px #dddddd; padding:10px;">
					<div class="row">
						<div class="col-md-4">
							<strong><?=$group->name?></strong>
						</div>
						<div class="col-md-3">
							Plugins: <?=count(explode(',', $group->plugins))?>
						</div>
						<div class="col-md-3">
							<?php
							$count = 0;
							foreach ($wspo_rules as $r) 
							{
								$plugins = explode(',', $r->plugins);
								if (in_array($group->id, $plugins))
									$count++;
							}
							?>
							Rules: <?=$count?>
						</div>
						<div class="col-md-2">
							<a class="btn-edit-plugin-group" href="#" data-toggle="modal" data-target="#modal_add_edit_plugin_group" data-group="<?=$group->id?>" data-name="<?=$group->name?>" data-plugins="<?=$group->plugins?>"><i class="fa fa-pencil"></i></a>
							<a class="btn-delete-plugin-group" href="#" data-group="<?=$group->id?>" data-toggle="modal" data-target="#modal_delete_group"><i class="fa fa-trash"></i></a>
						</div>
					</div>
				</li>				
				<?php
			}
		}
			else {
			echo ('<div style="padding-top:10px;"><span>No Groups found.</span><a style="padding:0px;" class="btn btn-add-plugin-group" data-toggle="modal" data-target="#modal_add_edit_plugin_group">Create new Group</a></div>');
			}
		?>
		</ul>
		</fieldset>
		</div>
	</div>
	
	<div class="col-md-6">
	<div class="custom-box">
		<h3 class="custom-h3"><i class="fa fa-file-text fa-blue" aria-hidden="true"></i>Page Groups</h3>
		<button style="position: absolute; right: 35px; top: 35px;" class="btn btn-add-page-group" data-toggle="modal" data-target="#modal_add_edit_page_group">
				<i class="fa fa-plus" style="padding-right:5px;"></i>Add Group
			</button>  
		<fieldset class="custom_fieldset" style="margin-top:15px;">
		<legend class="custom_legend">Current Page Groups</legend>
		<ul>
		<?php
		if (count($wspo_page_groups) > 0) {
		
			foreach($wspo_page_groups as $group)
			{
				?>
				<li style="border-bottom:solid 1px #dddddd; padding:10px;">
					<div class="row">
						<div class="col-md-4">
							<strong><?=$group->name?></strong>
						</div>
						<div class="col-md-3">
							Pages: <?=count(explode(',', $group->pages))?>
						</div>
						<div class="col-md-3">
							<?php
							$count = 0;
							foreach ($wspo_rules as $r) 
							{
								if ($r->type == '3' && $r->arg == $group->id)
									$count++;
							}
							?>
							Rules: <?=$count?>
						</div>
						<div class="col-md-2">
							<a class="btn-edit-page-group" href="#" data-toggle="modal" data-target="#modal_add_edit_page_group" data-group="<?=$group->id?>" data-name="<?=$group->name?>" data-pages="<?=$group->pages?>"><i class="fa fa-pencil"></i></a>
							<a class="btn-delete-page-group" href="#" data-toggle="modal" data-target="#modal_delete_group" data-group="<?=$group->id?>"><i class="fa fa-trash"></i></a>
						</div>
					</div>
				</li>				
				<?php
			}
			
		} else {
			echo ('<div style="padding-top:10px;"><span>No Groups found.</span><a style="padding:0px;" class="btn btn-add-page-group" data-toggle="modal" data-target="#modal_add_edit_page_group">Create new Group</a></div>');
			}
		?>
		</ul>
		</fieldset>
	</div></div>
</div>