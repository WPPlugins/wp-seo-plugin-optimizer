<?php
global $wpdb;

$pl_count = count($wspo_plugins);
?>

<div class="row">
	<div class="col-sm-12">
		<div class="custom-box">
			<h3 class="custom-h3"><i class="fa fa-area-chart fa-blue" aria-hidden="true"></i>Plugin Performance Monitoring
						<a style="margin-left:10px;" href="#" data-html="true" data-toggle="tooltip" title="<strong>View:</strong> Turn the rules off to see original Data of Plugins and Posts. Turn on to see the optimization results with active Rules.">
						<input class="wspo-toggle-lazy-type" data-on="With Rules" data-off="Without Rules" style="float:right;" type="checkbox" checked data-toggle="toggle">
						</a>
						</h3>
			
			<ul class="nav nav-tabs wspo_nav_tabs">
				<li class="active"><a data-toggle="tab" href="#wspo_monitoring_plugin">Plugin Overview</a></li>
				<li><a data-toggle="tab" href="#wspo_monitoring_load_time">Stats</a></li>
						
			</ul>
			
			
			<div class="tab-content">
				<div id="wspo_monitoring_plugin" class="tab-pane fade in active">
					<?php include 'view-plugin-monitoring.php'; ?>
				</div>
				<div id="wspo_monitoring_load_time" class="tab-pane fade">
					<?php include 'view-monitoring-load-time.php'; ?>
				</div>
			</div>
		</div>		
	</div>
</div>