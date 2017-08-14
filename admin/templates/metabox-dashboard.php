<?php
?>
	<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.8";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		
<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
	
	<div style="text-align:right; border-bottom:solid 1px #ddd; padding-bottom:5px;">
	<div class="fb-share-button" data-href="http://bavoko.services/wordpress/" data-layout="button" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fbavoko.services%2Fwordpress%2F&amp;src=sdkpreparse">Share</a></div>		
	<script type="IN/Share" data-url="http://bavoko.services/wordpress/"></script>
	</div>
	
	
	<div class="row" style="padding:15px 0;">
		
	<div style="display:flex;">
		
		<div class="col-sm-6 col-sm-height">
			<div class="wspo_stats_dashboard">
				<span class="stats_text">Saved Size (Avg.)</span></br>
				<span class="stats_number wspo-lazy-data-field" data-section="general" data-attribute="avg_save_size"></span> </br>
				Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="total_save_size"></span>	
			</div>	
		</div>
		
		<div class="col-sm-6 col-sm-height">
				
				<div class="wspo_stats_dashboard">
							<span class="stats_text">Saved Time (Avg.)</span></br>
							<span class="stats_number wspo-lazy-data-field" data-section="general" data-attribute="avg_save_time"></span> </br>
							Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="total_save_time"></span>	
				</div>
		</div>
		
		</div>
		<div style="display:flex; margin-top:10px;">	
	
		<div class="col-sm-6">
			<div class="wspo_stats_dashboard">
				<span class="stats_text">Saved Requests (Avg.)</span></br>
				<span class="stats_number wspo-lazy-data-field" data-section="general" data-attribute="avg_save_requests"></span> </br>
				Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="total_save_requests"></span>	
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="wspo_stats_dashboard">
				<span class="stats_text">Last Scan</span></br>
				<span class="stats_number"><?=empty($wspo_scans) ? '-' : date("Y-m-d", strtotime($wspo_scans[0]->timestamp));?></span></br>
				<a href="<?=admin_url('admin.php?page=wspo_plugin_role_main&show=scan')?>">Scan now!</a>		
			</div>
		</div>
		</div>
		</div>
		
		<div class="row">
		<div class="col-sm-6">	
		<div class="wspo_stats_dashboard">
			<span class="stats_text">Avg. Page Efficiency </span></br>
			<span class="stats_number"><span class="wspo-lazy-data-field" data-section="general" data-attribute="total_page_eff"></span> %</span>
			<a data-placement="right" href="#" data-toggle="tooltip" title="This value shows you the average ratio of plugins, that are loaded on a page, compared to the plugins, that are actually necessary.">
						<i style="padding-left:5px;" class="fa fa-info-circle item-blue" aria-hidden="true"></i>
					</a>
			<?php /*
			<div class="wspo_plugin_stats" style="height:40px; padding-top:5px; border:none; border-top:solid 1px #ddd; margin-top:10px;">
				<div style="float:left;" class="wspo-lazy-data-field" data-section="general" data-target="template">
					<a data-placement="right" href="#" data-toggle="tooltip" title="On Average, a Page loads %avg_page_loaded% Plugins, from which %avg_page_used% Plugins are needed.">
						<i class="fa fa-circle item-green" aria-hidden="true"><span>%avg_page_used%</span></i>
					</a>
				</div>
				<div class="wspo-lazy-data-field" data-section="general" data-target="template">
					<a data-placement="right" href="#" data-toggle="tooltip" title="On Average a Page loads %avg_page_loaded% Plugins, from which %avg_page_unused% Plugins are unnecessary.">
						<i class="fa fa-circle item-red" aria-hidden="true"><span>%avg_page_unused%</span></i>
					</a>
				</div>
			<span style="float:left" class="font-unimportant">Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="avg_page_loaded"></span>Plugins per Page</span>
			</div>
			*/ ?>
		</div>
		</div>

		<div class="col-sm-6">
		<div class="wspo_stats_dashboard">
			<span class="stats_text">Avg. Plugin Efficiency</span></br>
			<span class="stats_number"><span class="wspo-lazy-data-field" data-section="general" data-attribute="total_eff"></span> %</span>
			<a data-placement="right" href="#" data-toggle="tooltip" title="This value shows you the average ratio of pages, a plugin is loaded on, compared to the pages, this plugin is actually necessary.">
						<i style="padding-left:5px;" class="fa fa-info-circle item-blue" aria-hidden="true"></i>
					</a>
			<?php /*
			<div class="wspo_plugin_stats" style="height:40px; padding-top:5px; border:none; border-top:solid 1px #ddd; margin-top:10px;">
				<div style="float:left;" class="wspo-lazy-data-field" data-section="general" data-target="template">
					<a href="#" data-toggle="tooltip" data-placement="right" title="On Average, a Plugin is loaded on %avg_loaded% Pages, and needed on %avg_used% Pages">
						<i class="fa fa-circle item-green" aria-hidden="true"><span>%avg_used%</span></i>
					</a>
				</div>
				<div class="wspo-lazy-data-field" data-section="general" data-target="template">
					<a href="#" data-toggle="tooltip" data-placement="right" title="On Average, a Plugin is loaded on %avg_loaded% Pages, but unnecessary on %avg_unused% Pages">
						<i class="fa fa-circle item-red" aria-hidden="true"><span>%avg_unused%</span></i>
					</a>
				</div>
			<span class="font-unimportant" style="float:left;">Total: <span class="wspo-lazy-data-field" data-section="general" data-attribute="avg_loaded"></span>Pages per Plugin</span>
			</div>	
			*/ ?>

		</div>
		</div>
		</div>