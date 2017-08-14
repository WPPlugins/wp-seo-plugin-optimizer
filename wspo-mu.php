<?php
global $wspo_init_done, $wspo_verified;
$wspo_init_done = false;

function wspo_disable_plugin(&$plugins, $path)
{
	$key = array_search( $path , $plugins );

	if ( false !== $key ) {
		unset( $plugins[$key] );
	}
}

function wspo_disable_plugin_routine($plugins)
{
	require (ABSPATH . WPINC . '/pluggable.php');
	global $wspo_init_done;
	
	if ($wspo_init_done == false)
		$wspo_init_done = true;
	else
		return $plugins;
	
	if (get_option('wspo_scan_running'))
		return $plugins;
	
	global $wpdb;
	
	$actual_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$temp = explode('?', $actual_url, 2);
	$actual_link = $temp[0];
	$actual_request = isset($temp[1]) ? $temp[1] : '';
	$actual_uri = str_replace(home_url(), '', $actual_link);
	
	$current_user = get_userdata(get_current_user_id());
	
	$plugin_rules = $wpdb->get_results("
		SELECT *
		FROM wspo_plugin_rules
		");
	
	foreach ($plugin_rules as $r) 
	{
		$roles_rules = explode(',', $r->roles);
		$r_plugins = explode(',', $r->plugins);
		
		$disable = false;
		
		switch ($r->type)
		{
			case 0:
				$url = $wpdb->get_var($wpdb->prepare( 
					"
						SELECT url
						FROM wspo_page_register
						WHERE type = %s
						AND arg = %s
					", 
					'1',
					$r->arg
				));
				
				if ($actual_link == $url)
				{
					$disable = true;
				}
				break;
				
			case 1:
				$args = explode(',', $r->arg);
				$url = $wpdb->get_var($wpdb->prepare( 
					"
						SELECT url
						FROM wspo_page_register
						WHERE type = %s
						AND arg = %s
					", 
					'0',
					$args[0]
				));
				
				if ($args[0] == 'post' || $args[0] == 'page')
				{
					$id = $wpdb->get_var($wpdb->prepare( 
						"
							SELECT arg
							FROM wspo_page_register
							WHERE type = %s
							AND url = %s
						",
						"1",
						$actual_link
					));
					
					if ($id && get_post_type($id) == $args[0])
					{
						$disable = true;
					}
				}
				else
				{
					if (($args[1] == 'archive' && $actual_uri == $url) ||
						($args[1] == 'single' && substr($actual_uri, 0, strlen($url)) === $url))
						$disable = true;
				}
					
				break;
				
			case 2: //Regex
				$args = explode(',', $r->arg, 3);
				
				$base = '';
				
				if (count($args) == 3)
				{
					switch ($args[0])
					{
						case 0: //Link
							$base = $actual_link;
							break;
						case 1: //Link + GET
							$base = $actual_url;
							break;
						case 2: //URI
							$base = $actual_uri;
							break;
						case 3: //URI + GET
							$base = $actual_uri . '?' . $actual_request;
							break;
						case 4: //GET
							$base = '?' . $actual_request;
							break;
					}
					
					switch ($args[1])
					{
						case 0: //Perfect Match
							if ($base == $args[2])
							{
								$disable = true;
							}
							break;
						case 1: //Contains
							if (strpos($base, $args[2]) !== false)
							{
								$disable = true;
							}
							break;
						case 2: //Starts With
							if (substr($base, 0, strlen($args[2])) === $args[2])
							{
								$disable = true;
							}
							break;
						case 3: //Ends With
							if (substr($base, strlen($base) - strlen($args[2]), strlen($base)) === $args[2])
							{
								$disable = true;
							}
							break;
						case 4: //Regex
							if (preg_match($args[2], $base))
							{
								$disable = true;
							}
							break;
					}
				}
				break;
				
			case 3:
				$group = $wpdb->get_row('SELECT * FROM wspo_page_groups WHERE id = ' . $r->arg);
				
				if ($group)
				{
					$pages = explode(',', $group->pages);
					
					foreach ($pages as $page)
					{
						$url = $wpdb->get_var($wpdb->prepare( 
							"
								SELECT url
								FROM wspo_page_register
								WHERE type = %s
								AND arg = %s
							", 
							'1',
							$page
						));
						
						if ($actual_link == $url)
						{
							$disable = true;
							break;
						}
					}
				}
				break;
		}
		
		if ($disable == true && !empty($roles_rules))
		{
			$disable = false;
			
			if (in_array('-2', $roles_rules))
			{
				$disable = true;
			}
			else
			{
				if (is_user_logged_in())
				{
					if (in_array('0', $roles_rules))
					{
						$disable = true;
					}
					else if (!empty($current_user->roles))
					{
						foreach ($current_user->roles as $role)
						{
							if (in_array($role, $roles_rules))
							{
								$disable = true;
								break;
							}
						}
					}
				}
				else if (in_array('-1', $roles_rules))
				{
					$disable = true;
				}
			}
		}
	
		if ($disable == true)
		{
			foreach ($r_plugins as $plugin)
			{
				if (is_numeric($plugin)) //Is group
				{
					$group = $wpdb->get_row( "SELECT * FROM wspo_plugin_groups WHERE id = " . $plugin );
					$pl = explode(',', $group->plugins);
					
					foreach ($pl as $pl_t)
					{
						wspo_disable_plugin($plugins, $pl_t);
					}
				}
				else
				{
					wspo_disable_plugin($plugins, $plugin);
				}
			}
		}
	}
			
	return $plugins;
}
add_filter( 'option_active_plugins', 'wspo_disable_plugin_routine' );
?>