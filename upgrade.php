<?php
if (!$wspo_data || !is_array($wspo_data))
{
	wspo_install_plugin();
	$wspo_data = array(
		'version' => WSPO_VERSION,
		'add_bootstrap' => true,
		'add_fontawesome' => true,
		'add_bootstrap_select' => true,
		'add_bootstrap_toggle' => true,
		'add_load_chart' => true,
		'add_google_chart' => true,
		'system_plugins' => array(),
		'plugin_path' => plugin_dir_path( __FILE__ ),
	);
	
	if ($wspo_data)
		update_option('wspo_init', $wspo_data);
	else
		add_option('wspo_init', $wspo_data);
}
else
{
	if (version_compare($wspo_data['version'], WSPO_VERSION, '<'))
	{
		if (version_compare($wspo_data['version'], '1.1', '<'))
		{
			delete_option('wspo_init');
			
			delete_option('bv_add_bootstrap');
			delete_option('bv_add_fontawesome');
			delete_option('bv_add_bootstrap_select');
			delete_option('bv_add_bootstrap_toggle');
			delete_option('bv_add_load_chart');
			delete_option('bv_add_google_chart');
			$res = copy(plugin_dir_path( __FILE__ ) . '/wspo-mu-load.php', WPMU_PLUGIN_DIR . '/wspo-mu-load.php');
		}
			
		$wspo_data = array(
			'version' => WSPO_VERSION,
			'add_bootstrap' => true,
			'add_fontawesome' => true,
			'add_bootstrap_select' => true,
			'add_bootstrap_toggle' => true,
			'add_load_chart' => true,
			'add_google_chart' => true,
			'scan_draft' => false,
			'activate_dashboard_widget' => true,
			'system_plugins' => array(),
			'plugin_path' => plugin_dir_path( __FILE__ ),
		);
		
		if (get_option('wspo_init'))
			update_option('wspo_init', $wspo_data);
		else
			add_option('wspo_init', $wspo_data);
	}
}
?>