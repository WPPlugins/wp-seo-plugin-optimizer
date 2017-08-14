<?php
global $wspo_admin_page;

class WSPO_AdminMenu
{
	static $instance;
	
	public function __construct()
	{
		add_action('admin_menu', [$this, 'menu_items']);
		add_action('admin_enqueue_scripts', [$this, 'load_scripts']);
		add_action('wp_dashboard_setup', [$this, 'add_dashboard_meta_boxes']);
	}

	public static function set_screen($status, $option, $value)
	{
		return $value;
	}

	public function menu_items()
	{
		$hook = add_menu_page(
			'WSPO - Dashboard',
			'WP SEO Plugin Optimizer',
			'manage_options',
			'wspo_plugin_role_main',
			[ $this, 'main_view' ]
		);
		
		$hook = add_submenu_page(
			'wspo_plugin_role_main',
			'WSPO - Settings',
			'Settings',
			'manage_options',
			'wspo_plugin_role_settings',
			[ $this, 'settings_view' ]
		);
		
		/* $hook = add_submenu_page(
			'wspo_plugin_role_main',
			'WSPO - Pro',
			'Be a Pro!',
			'manage_options',
			'wspo_plugin_pro',
			[ $this, 'pro_view' ]
		); */
	}
	
	public function load_scripts($hook)
	{
		global $wspo_data;
			
		if ($hook == 'index.php' && (!isset($wspo_data['activate_dashboard_widget']) || !$wspo_data['activate_dashboard_widget']))
			return;
		
		$admin_page = false;
		if (isset($_GET['page']) && in_array($_GET['page'], array('wspo_plugin_role_main', 'wspo_plugin_role_settings', 'wspo_plugin_pro', 'wspo_plugin_role_activate')))
		{
			$admin_page = true;
		}
		
		if ($hook == 'post.php' || $hook == 'index.php' || $admin_page)
		{
			if (isset($wspo_data['add_bootstrap']) && $wspo_data['add_bootstrap'])
			{
				wp_register_script('bootstrap_js', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
				wp_enqueue_script('bootstrap_js');
				
				wp_register_style('bootstrap_css', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
				wp_enqueue_style('bootstrap_css');
			}
			
			if (isset($wspo_data['add_fontawesome']) && $wspo_data['add_fontawesome'])
			{
				wp_register_style('font_awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
				wp_enqueue_style('font_awesome');
			}
			
			if ($hook != 'index.php')
			{
				if (isset($wspo_data['add_bootstrap_select']) && $wspo_data['add_bootstrap_select'])
				{
					wp_register_script('bootstrap_select_js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js');
					wp_enqueue_script('bootstrap_select_js');
					
					wp_register_style('bootstrap_select_css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css');
					wp_enqueue_style('bootstrap_select_css');
				}
				
				if (isset($wspo_data['add_bootstrap_toggle']) && $wspo_data['add_bootstrap_toggle'])
				{
					wp_register_script('bootstrap_toggle_js', 'https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js');
					wp_enqueue_script('bootstrap_toggle_js');
					
					wp_register_style('bootstrap_toggle_css', 'https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css');
					wp_enqueue_style('bootstrap_toggle_css');
				}
				
				if (isset($wspo_data['add_load_chart']) && $wspo_data['add_load_chart'])
				{
					wp_register_script('load_chart', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.bundle.min.js');
					wp_enqueue_script('load_chart');
				}
				
				if (isset($wspo_data['add_google_chart']) && $wspo_data['add_google_chart'])
				{
					wp_register_script('google_charts', 'https://www.gstatic.com/charts/loader.js');
					wp_enqueue_script('google_charts');
				}
				
				wp_enqueue_script('wspo_backend_js', plugins_url('admin/js/backend.js' , dirname(__FILE__)));
				wp_enqueue_script('wspo_charts_js', plugins_url('admin/js/charts.js' , dirname(__FILE__)));
				
				wp_enqueue_script('wspo_activate_js', plugins_url('admin/js/activate.js' , dirname(__FILE__)));
			}
				
			wp_register_style('wspo_admin_css', plugins_url('admin/css/admin.css',dirname(__FILE__ )));
			wp_enqueue_style('wspo_admin_css');
			
			wp_enqueue_script('wspo_lazy_js', plugins_url('admin/js/lazy.js' , dirname(__FILE__)));
		}
		
		if ($admin_page)
		{
			wp_enqueue_script('wspo_admin_js', plugins_url('admin/js/admin.js' , dirname(__FILE__)));
		}
	}
	
	public function add_dashboard_meta_boxes()
	{
		global $wspo_data;
		
		if (!isset($wspo_data['activate_dashboard_widget']) || !$wspo_data['activate_dashboard_widget'])
			return;
		
		wp_add_dashboard_widget(
			 'wspo_dashboard',
			 'WP SEO Plugin Optimizer',
			 array( &$this, 'dashboard_meta_box_view')
        );	
	}
	
    public function dashboard_meta_box_view() 
    {
		global $wpdb;
		
		$wspo_path = plugin_dir_path( __FILE__ );
		
		$wspo_scans = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_scans_auto
			ORDER BY timestamp DESC
			"
		);
		
		include( $wspo_path. '/templates/metabox-dashboard.php');
	}
	
	public function main_view()
	{
		global $wpdb;
		$wspo_path = plugin_dir_path( __FILE__ );
		$wspo_post_types = get_post_types('', 'objects');
		$wspo_post_types_all = $wspo_post_types;
		foreach ($wspo_post_types as $key=>$type)
			if (!$type->rewrite && $key != 'post' && $key != 'page')
				unset($wspo_post_types[$key]);
			
		$wspo_plugins = get_plugins();
		
		$wspo_rules = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_plugin_rules
			"
		);
		
		$wspo_regions = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_plugin_regions
			"
		);
		
		$wspo_scans = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_scans_auto
			ORDER BY timestamp DESC
			"
		);
		
		$wspo_plugin_groups = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_plugin_groups
			"
		);
		
		$wspo_page_groups = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_page_groups
			"
		);
		
		/*$wspo_rule_performance = array();
		
		foreach ($wspo_rules as $t_rule)
		{
			$wspo_rule_performance[(string)$t_rule->id] = wspo_get_rule_performance($wspo_scans, $t_rule);
		}*/
		
		$show = isset($_GET['show']) ? sanitize_text_field($_GET['show']) : false;
		$wspo_scan = isset($_GET['scan_id']) ? intval(sanitize_text_field($_GET['scan_id'])) : false;
		
		if ($wspo_scan)
		{
			if (isset($_GET["res"]))
			{
				if ($_GET["res"])
				{ ?>
					<div class="updated">
						<p><?=isset($_GET['msg']) ? $_GET['msg'] : 'Execution successfull. Rules added.'?></p>
					</div>
				<?php }
				else
				{ ?>
					<div class="updated">
						<p><?=isset($_GET['msg']) ? $_GET['msg'] : 'Execution failed.'?></p>
					</div>
				<?php }
			}
			$scan = $wpdb->get_row("SELECT * FROM wspo_scans_auto WHERE id = " . $wspo_scan);
			?>
			<div class="wrap">
				<div class="col-sm-12">
				<h2><img class="wspo_logo" src="<?=plugins_url('/img/Signet.png', __FILE__)?>" />WP SEO Plugin Optimizer - Scan</h2>
				</div>
				
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-9">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable" style="padding:20px">
							
							<ul class="main-nav nav nav-pills" style="border-bottom:solid 1px #dddddd; padding-bottom:5px;">
									<li><a class="btn" href="<?=admin_url('admin.php?page=wspo_plugin_role_main&show=scans')?>"><i class="fa fa-reply" aria-hidden="true"></i> Back to Dashboard</a>
							</ul>
								<?php 
								if ($scan)
								{
									if ($scan->finished)
									{
										?><div id="wspo_lazy_beacon" style="display:none" data-nonce="<?=wp_create_nonce('wspo-lazy-performance')?>"></div><?php
										include( $wspo_path. '/templates/view-scan.php'); 
									}
									else
									{
										?>
										<div class="col-md-12">
											<div class="custom-box" style="padding:15px;">
												<h3 class="custom-h3"><i class="fa fa-eye fa-blue" aria-hidden="true"></i>Aborted Scan</h3>
												You are trying to view an aborted scan. Please leave.
											</div>
										</div>
										<?php
									}
								}
								else
								{ ?>
									<div class="col-md-12">
										<div class="custom-box" style="padding:15px;">
											<h3 class="custom-h3"><i class="fa fa-eye fa-blue" aria-hidden="true"></i>Wrong Scan ID</h3>
											You are trying to view a non-existent scan. Please leave.
										</div>
									</div>
								<?php }	?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			if ($scan)
			{
				include( $wspo_path. '/templates/modal-edit-scan-rule.php');
				include( $wspo_path. '/templates/modal-delete-scan.php');
				include( $wspo_path. '/templates/modal-pro-rules-scan.php');
			}
		}
		else
		{
			?>
			<div class="wrap">
				<div class="col-sm-12">
				<h2><img class="wspo_logo" src="<?=plugins_url('/img/Signet.png', __FILE__)?>" />WP SEO Plugin Optimizer - Dashboard</h2>
				</div>

				
				
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-9">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable" style="padding:20px">
								<?php
								if (!$wspo_scans || empty($wspo_scans))
								{
									include( $wspo_path. '/templates/view-scan-first.php');
								}
								else
								{
									?>
									<div id="wspo_lazy_beacon" style="display:none" data-nonce="<?=wp_create_nonce('wspo-lazy-performance')?>"></div>
									<div id="wspo_scan_beacon" style="display:none" data-nonce="<?=wp_create_nonce('wspo-auto-scan')?>" data-nonce-finish="<?=wp_create_nonce('wspo-auto-scan-finish')?>"></div>
									
									<ul class="main-nav nav nav-pills" style="border-bottom:solid 1px #dddddd; padding-bottom:5px;">
										<li <?=$show ? '' : 'class="active"' ?>><a data-toggle="tab" href="#wspo_view_info">Overview</a></li>
										<li <?=$show == 'monitoring' ? 'class="active"' : ''  ?>><a data-toggle="tab" href="#wspo_plugin_monitoring">Monitoring</a></li>
										<li <?=$show == 'scans' ? 'class="active"' : '' ?>><a id="custom_view_scan" data-toggle="tab" href="#wspo_view_scans">Scans</a></li>
										<li id="custom_view_rules" <?=$show == 'rules' ? 'class="active"' : '' ?>><a data-toggle="tab" href="#wspo_view_rules">Manage Rules</a></li>
										<li <?=$show == 'groups' ? 'class="active"' : '' ?>><a data-toggle="tab" href="#wspo_view_plugin">Manage Groups</a></li>
										<li style="float:right;"><a  target="_blank" href="http://bavoko.services/wspo/" style="color:#fff;" class="item-background-green" href="#wspo_view_plugin"><i class="fa fa-rocket" style="padding-right:10px; font-size:16px;" aria-hidden="true"></i>Go Pro!</a></li>

									</ul>
									
									<div class="tab-content">
										<div id="wspo_view_info" class="tab-pane fade <?=$show ? '' : 'in active' ?>">
											<?php include( $wspo_path. '/templates/view-overview.php'); ?>
										</div>
										<div id="wspo_plugin_monitoring" class="tab-pane fade <?=$show == 'monitoring' ? 'in active' : '' ?>">
											<?php include( $wspo_path. '/templates/view-monitoring.php'); ?>
										</div>
										<div id="wspo_view_rules" class="tab-pane fade <?=$show == 'rules' ? 'in active' : '' ?>">
											<?php include( $wspo_path. '/templates/view-rules.php'); ?>
										</div>
										<div id="wspo_view_scans" class="tab-pane fade <?=$show == 'scans' ? 'in active' : '' ?>">
											<?php include( $wspo_path. '/templates/view-scans.php'); ?>
										</div>
										<div id="wspo_view_plugin" class="tab-pane fade <?=$show == 'groups' ? 'in active' : '' ?>">
											<?php include( $wspo_path. '/templates/view-groups.php'); ?>
										</div>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			include( $wspo_path. '/templates/modal-add-edit-rule.php');
			include( $wspo_path. '/templates/modal-add-edit-plugin-group.php');
			include( $wspo_path. '/templates/modal-add-edit-page-group.php');
			include( $wspo_path. '/templates/modal-delete-rule.php');
			include( $wspo_path. '/templates/modal-delete-scan.php');
			include( $wspo_path. '/templates/modal-delete-region.php');
			include( $wspo_path. '/templates/modal-delete-group.php');
			include( $wspo_path. '/templates/modal-show-monitoring-data.php');
			include( $wspo_path. '/templates/modal-pro-rules.php');
			include( $wspo_path. '/templates/modal-pro-groups.php');
		}
	}

	public function settings_view()
	{
		$wspo_path = plugin_dir_path( __FILE__ );
		
		$wspo_plugins = get_plugins();
		
		if (isset($_GET["res"]))
		{
			if ($_GET["res"])
			{ ?>
				<div class="updated">
					<p><?=isset($_GET['msg']) ? $_GET['msg'] : 'Save success.'?></p>
				</div>
			<?php }
			else
			{ ?>
				<div class="updated">
					<p><?=isset($_GET['msg']) ? $_GET['msg'] : 'Save failed.'?></p>
				</div>
			<?php }
		} ?>
		<div class="wrap">
			<div class="col-sm-12">
				<h2><img class="wspo_logo" src="<?=plugins_url('/img/Signet.png', __FILE__)?>" />WP SEO Plugin Optimizer - Settings</h2>
				</div>
			
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-9">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable" style="padding:20px">
							<div id="wspo_view_settings_general" class="tab-pane fade in active">
								<?php include( $wspo_path. '/templates/view-settings.php'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function pro_view()
	{
		$wspo_path = plugin_dir_path( __FILE__ );
		?>
		<div class="wrap">
			<div class="col-sm-12">
				<h2><img class="wspo_logo" src="<?=plugins_url('/img/Signet.png', __FILE__)?>" />WP SEO Plugin Optimizer - Be a Pro!</h2>
				</div>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-9">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable" style="padding:20px">
							<?php include( $wspo_path. '/templates/view-pro.php'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	public static function get_instance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}
}

add_action('plugins_loaded', function()
{
	WSPO_AdminMenu::get_instance();
});

?>