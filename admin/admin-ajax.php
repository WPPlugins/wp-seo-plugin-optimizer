<?php
function wspo_lazy_performance_data() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-lazy-performance');
		
		ob_start();
		global $wpdb;
		
		$wspo_plugins = get_plugins();
		$wspo_rules = $wpdb->get_results( 
				"
				SELECT *
				FROM wspo_plugin_rules
				"
			);
		$wspo_scans = $wpdb->get_results( 
			"
			SELECT *
			FROM wspo_scans_auto
			ORDER BY timestamp DESC
			"
		);
		
		$wspo_post_types = get_post_types('', 'objects');
		$wspo_post_types_all = $wspo_post_types;
		foreach ($wspo_post_types as $key=>$type)
			if (!$type->rewrite && $key != 'post' && $key != 'page')
				unset($wspo_post_types[$key]);
		
		$with_rule = $_REQUEST['type'] == '0' ? true : false;
		
		//Montoring 
		$wspo_monitoring = array(
			'runtime_plugin' => array(),
			'runtime_summary' => array(),
			'runtime_summary_light' => array(),
			'runtime_file_type' => array()
		);
		
		//Rules
		$count_rules = 0;
		$total_size_js = 0;
		$total_size_css = 0;
		$total_requests_js = 0;
		$total_requests_css = 0;
		$total_time_js = 0;
		$total_time_css = 0;
		$top_rules_size = array();
		$top_rules_requests = array();
		$wspo_rule_performance = array();
		foreach ($wspo_rules as $t_rule)
		{
			$count_rules++;
			$perf = wspo_get_rule_performance($wspo_scans, $t_rule);
			if ($perf['res'])
			{
				$total_requests_js += $perf['total_js'];
				$total_requests_css += $perf['total_css'];
				
				$total_size_js += $perf['total_js_size'];
				$total_size_css += $perf['total_css_size'];
				
				$total_time_js += $perf['total_js_time'];
				$total_time_css += $perf['total_css_time'];
				
				$saved_requests = $perf['total_css'] + $perf['total_js'];
				$saved_size = wspo_format_byte($perf['total_css_size'] + $perf['total_js_size']);
				$saved_time = wspo_format_time($perf['total_css_time'] + $perf['total_js_time']);
				
				$wspo_rule_performance[(string)$t_rule->id] =  array(
					'total_requests' => $saved_requests,
					'total_size' => $saved_size,
					'total_time' => $saved_time
					);
				
				switch ($t_rule->type)
				{
					case '0':
						$rule_title = 'Page Rule for ' . get_the_title($t_rule->arg);
						break;
					case '1':
						$args = explode(',', $t_rule->arg);
						$p_obj = get_post_type_object($args[0]);
						$rule_title = 'Post Type Rule (' . $args[1] . ') for ' . $p_obj->label;
						break;
					case '2':
						$args = explode(',', $t_rule->arg);
						$rule_title = 'Regex Rule (' . $args[0] . '|' . $args[1] . ') for ' . $args[2];
						break;
					case '3':
						$rule_title = 'Page Rule for Group ' . $wpdb->get_var('SELECT name FROM wspo_page_groups WHERE id=' . $t_rule->arg);
						break;
					 
				}
				$rule_title = '<a href="' . admin_url('admin.php?page=wspo_plugin_role_main&show=rules&region_id=' . $t_rule->region_id) . '">' . $rule_title . '</a>';
				$plugins = explode(',', $t_rule->plugins);
				$plugin_str = '';
				foreach ($plugins as $plugin)
				{
					$plugin = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
					if ($plugin)
						$plugin_str .= $plugin['Name'] . '<br/>';
				}
				$count_plugins = count($plugins);
				
				$top_rules_requests[(string)$t_rule->id] = array( 'title' => $rule_title, 'plugins' => $count_plugins, 'plugin_str' => $plugin_str, 'saved' => $saved_requests, 'saved_css' => $perf['total_css'],  'saved_js' => $perf['total_js']);
				$top_rules_size[(string)$t_rule->id] = array( 'title' => $rule_title, 'plugins' => $count_plugins, 'plugin_str' => $plugin_str, 'saved' => $saved_size, 'saved_css' => wspo_format_byte($perf['total_css_size']),  'saved_js' => wspo_format_byte($perf['total_js_size']));
			}
		}
		
		uasort($top_rules_size, function($a, $b)
		{
			if ($a['saved'] == $b['saved'])
				return 0;
			return $a['saved'] < $b['saved'] ? 1 : -1;
		});
		array_splice($top_rules_size, 5);
		
		uasort($top_rules_requests, function($a, $b)
		{
			if ($a['saved'] == $b['saved'])
				return 0;
			return $a['saved'] < $b['saved'] ? 1 : -1;
		});
		array_splice($top_rules_requests, 5);
		
		//Plugins
		$pl_css_runtime = 0;
		$pl_js_runtime = 0;
		$total_eff = 0;
		$total_eff_count = 0;
		$avg_used = 0;
		$avg_loaded = 0;
		$top_ineff_plugins = array();
		$wspo_plugin_performance = array();
		$wspo_page_performance = array();
		foreach ($wspo_plugins as $key => $plugin)
		{
			$perf = wspo_get_plugin_performance($wspo_scans, $key, $with_rule);
			$pl_obj = get_plugin_data(WP_PLUGIN_DIR . '/' . $key);
			$pl_requests_css = 0;
			$pl_requests_js = 0;
			$pl_size_css = 0;
			$pl_size_js = 0;
			$pl_time_css = 0;
			$pl_time_js = 0;
			$used_pages = array();
			$unused_pages = array();
			foreach ($perf['used_pages'] as $pl_page)
			{
				if (!isset($wspo_page_performance[$pl_page['page']]))
				{
					$wspo_page_performance[$pl_page['page']] = array('used_plugins' => array(), 'unused_plugins' => array());
				}
				$wspo_page_performance[$pl_page['page']]['used_plugins'][$key] = array(
					'name' => $pl_obj['Name'],
					'request' => $pl_page['data']->css_files + $pl_page['data']->js_files,
					'request_css' => $pl_page['data']->css_files,
					'request_js' => $pl_page['data']->js_files,
					'size' => wspo_format_byte($pl_page['data']->css_size + $pl_page['data']->js_size),
					'size_css' => wspo_format_byte($pl_page['data']->css_size),
					'size_js' => wspo_format_byte($pl_page['data']->js_size),
					'time' => wspo_format_time($pl_page['data']->css_time + $pl_page['data']->js_time),
					'time_css' => wspo_format_time($pl_page['data']->css_time),
					'time_js' => wspo_format_time($pl_page['data']->js_time),
				);
				$wspo_page_performance[$pl_page['page']]['total_request'] += $pl_page['data']->css_files + $pl_page['data']->js_files;
				$wspo_page_performance[$pl_page['page']]['total_request_css'] += $pl_page['data']->css_files;
				$wspo_page_performance[$pl_page['page']]['total_request_js'] += $pl_page['data']->js_files;
				$wspo_page_performance[$pl_page['page']]['total_size'] += $pl_page['data']->css_size + $pl_page['data']->js_size;
				$wspo_page_performance[$pl_page['page']]['total_size_css'] += $pl_page['data']->css_size;
				$wspo_page_performance[$pl_page['page']]['total_size_js'] += $pl_page['data']->js_size;
				$wspo_page_performance[$pl_page['page']]['total_time'] += $pl_page['data']->css_time + $pl_page['data']->js_time;
				$wspo_page_performance[$pl_page['page']]['total_time_css'] += $pl_page['data']->css_time;
				$wspo_page_performance[$pl_page['page']]['total_time_js'] += $pl_page['data']->js_time;
				
				array_push($used_pages, array(
					'name' => get_the_title((int)$pl_page['page']),
					'request' => $pl_page['data']->css_files + $pl_page['data']->js_files,
					'request_css' => $pl_page['data']->css_files,
					'request_js' => $pl_page['data']->js_files,
					'size' => wspo_format_byte($pl_page['data']->css_size + $pl_page['data']->js_size),
					'size_css' => wspo_format_byte($pl_page['data']->css_size),
					'size_js' => wspo_format_byte($pl_page['data']->js_size),
					'time' => wspo_format_time($pl_page['data']->css_time + $pl_page['data']->js_time),
					'time_css' => wspo_format_time($pl_page['data']->css_time),
					'time_js' => wspo_format_time($pl_page['data']->js_time),
					));
			}
			
			foreach ($perf['loaded_pages'] as $pl_page)
			{
				if (!isset($wspo_page_performance[$pl_page['page']]))
				{
					$wspo_page_performance[$pl_page['page']] = array('used_plugins' => array(), 'unused_plugins' => array());
				}
				
				if (!isset($wspo_page_performance[$pl_page['page']]['used_plugins'][$key]) && !isset($wspo_page_performance[$pl_page['page']]['used_plugins'][$key]))
				{
					$wspo_page_performance[$pl_page['page']]['unused_plugins'][$key] = array(
						'name' => $pl_obj['Name'],
						'request' => $pl_page['data']->css_files + $pl_page['data']->js_files,
						'request_css' => $pl_page['data']->css_files,
						'request_js' => $pl_page['data']->js_files,
						'size' => wspo_format_byte($pl_page['data']->css_size + $pl_page['data']->js_size),
						'size_css' => wspo_format_byte($pl_page['data']->css_size),
						'size_js' => wspo_format_byte($pl_page['data']->js_size),
						'time' => wspo_format_time($pl_page['data']->css_time + $pl_page['data']->js_time),
						'time_css' => wspo_format_time($pl_page['data']->css_time),
						'time_js' => wspo_format_time($pl_page['data']->js_time),
					);
					$wspo_page_performance[$pl_page['page']]['total_ul_request'] += $pl_page['data']->css_files + $pl_page['data']->js_files;
					$wspo_page_performance[$pl_page['page']]['total_ul_request_css'] += $pl_page['data']->css_files;
					$wspo_page_performance[$pl_page['page']]['total_ul_request_js'] += $pl_page['data']->js_files;
					$wspo_page_performance[$pl_page['page']]['total_ul_size'] += $pl_page['data']->css_size + $pl_page['data']->js_size;
					$wspo_page_performance[$pl_page['page']]['total_ul_size_css'] += $pl_page['data']->css_size;
					$wspo_page_performance[$pl_page['page']]['total_ul_size_js'] += $pl_page['data']->js_size;
					$wspo_page_performance[$pl_page['page']]['total_ul_time'] += $pl_page['data']->css_time + $pl_page['data']->js_time;
					$wspo_page_performance[$pl_page['page']]['total_ul_time_css'] += $pl_page['data']->css_time;
					$wspo_page_performance[$pl_page['page']]['total_ul_time_js'] += $pl_page['data']->js_time;
				
					array_push($unused_pages, array(
						'name' => get_the_title((int)$pl_page['page']),
						'request' => $pl_page['data']->css_files + $pl_page['data']->js_files,
						'request_css' => $pl_page['data']->css_files,
						'request_js' => $pl_page['data']->js_files,
						'size' => wspo_format_byte($pl_page['data']->css_size + $pl_page['data']->js_size),
						'size_css' => wspo_format_byte($pl_page['data']->css_size),
						'size_js' => wspo_format_byte($pl_page['data']->js_size),
						'time' => wspo_format_time($pl_page['data']->css_time + $pl_page['data']->js_time),
						'time_css' => wspo_format_time($pl_page['data']->css_time),
						'time_js' => wspo_format_time($pl_page['data']->js_time),
						));
				}
				
				$pl_requests_css += $pl_page['data']->css_files;
				$pl_requests_js += $pl_page['data']->js_files;
				$pl_size_css += $pl_page['data']->css_size;
				$pl_size_js += $pl_page['data']->js_size;
				$pl_time_css += $pl_page['data']->css_time;
				$pl_time_js += $pl_page['data']->js_time;
			}
			
			$pl_pages_count = count($perf['used_pages']);
			$pl_loaded_pages_count = count($perf['loaded_pages']);
			
			$avg_pl_time = $pl_loaded_pages_count > 0 ? ($pl_time_css + $pl_time_js) / $pl_loaded_pages_count : 0;
			$avg_pl_css_time = $pl_loaded_pages_count > 0 ? $pl_time_css / $pl_loaded_pages_count : 0;
			$avg_pl_js_time = $pl_loaded_pages_count > 0 ? $pl_time_js / $pl_loaded_pages_count : 0;
			if ($pl_loaded_pages_count > 0)
			{
				$eff = round($pl_pages_count / $pl_loaded_pages_count * 100, 2);
				//$total_eff += $eff;
				$total_eff_count++;
				$avg_used += $pl_pages_count;
				$avg_loaded += $pl_loaded_pages_count;
				
				if ($eff < 70)
				{
					array_push($top_ineff_plugins, array('load_eff' => $eff, 'name' => $pl_obj['Name']));
				}
				
				array_push($wspo_monitoring['runtime_plugin'], array($pl_obj['Name'], array( 'v' => $avg_pl_time, 'f' => wspo_format_time($avg_pl_time))));
			}
			else
			{
				$eff = 0;
			}
			
			$pl_css_runtime += $avg_pl_css_time;
			$pl_js_runtime += $avg_pl_js_time;
			
			$wspo_plugin_performance[$key] = array(
				'used_on' => $pl_pages_count,
				'loaded_on' => $pl_loaded_pages_count,
				'unused_on' => $pl_loaded_pages_count - $pl_pages_count,
				'load_eff' => $eff,
				'avg_requests' => $pl_loaded_pages_count > 0 ? round(($pl_requests_css + $pl_requests_js) / $pl_loaded_pages_count, 2) : 0,
				'avg_css' => $pl_loaded_pages_count > 0 ? round($pl_requests_css / $pl_loaded_pages_count, 2) : 0,
				'avg_js' => $pl_loaded_pages_count > 0 ? round($pl_requests_js / $pl_loaded_pages_count, 2) : 0,
				'avg_size' => $pl_loaded_pages_count > 0 ? wspo_format_byte(($pl_size_css + $pl_size_js) / $pl_loaded_pages_count) : 0,
				'avg_css_size' => $pl_loaded_pages_count > 0 ? wspo_format_byte($pl_size_css / $pl_loaded_pages_count) : 0,
				'avg_js_size' => $pl_loaded_pages_count > 0 ? wspo_format_byte($pl_size_js / $pl_loaded_pages_count) : 0,
				'avg_time' => wspo_format_time($avg_pl_time),
				'avg_css_time' => wspo_format_time($avg_pl_css_time),
				'avg_js_time' => wspo_format_time($avg_pl_js_time),
				'used_pages' => $used_pages,
				'unused_pages' => $unused_pages,
				);
		}
		
		$avg_unused = $total_eff_count > 0 ? round(($avg_loaded - $avg_used) / $total_eff_count, 2) : 0;
		$avg_used = $total_eff_count > 0 ? round($avg_used / $total_eff_count, 2) : 0;
		$avg_loaded = $total_eff_count > 0 ? round($avg_loaded / $total_eff_count, 2) : 0;
		$total_eff = $avg_loaded > 0 ? round($avg_used / $avg_loaded * 100, 2) : 0;
		
		usort($top_ineff_plugins, function($a, $b)
		{
			if ($a['load_eff'] == $b['load_eff'])
				return 0;
			return $a['load_eff'] < $b['load_eff'] ? -1 : 1;
		});
		array_splice($top_ineff_plugins, 3);
		
		//Pages
		$total_page_eff = 0;
		$total_page_eff_count = 0;
		$avg_page_used = 0;
		$avg_page_unused = 0;
		$avg_page_loaded = 0;
		$total_time = 0;
		$avg_time = 0;
		$avg_save_time = 0;
		$page_avg_size = 0;
		$page_avg_saved_size = 0;
		$top_ineff_pages = array();
		foreach ($wspo_page_performance as $key => $p)
		{
			$wspo_page_performance[$key]['total_used'] = count($wspo_page_performance[$key]['used_plugins']);
			$wspo_page_performance[$key]['total_unused'] = count($wspo_page_performance[$key]['unused_plugins']);
			$wspo_page_performance[$key]['total_loaded'] = $wspo_page_performance[$key]['total_used'] + $wspo_page_performance[$key]['total_unused'];
			
			$avg_time += $wspo_page_performance[$key]['total_time_css'] + $wspo_page_performance[$key]['total_time_js'];
			
			if ($wspo_page_performance[$key]['total_loaded'] > 0)
			{
				$page_avg_size += $wspo_page_performance[$key]['total_ul_size'];
				$page_avg_saved_size += $wspo_page_performance[$key]['total_size'];
			
				$eff = round($wspo_page_performance[$key]['total_used'] / $wspo_page_performance[$key]['total_loaded'] * 100, 2);
				
				//$total_page_eff += $eff;
				$total_page_eff_count++;
				$avg_page_used += $wspo_page_performance[$key]['total_used'];
				$avg_page_unused += $wspo_page_performance[$key]['total_unused'];
				$avg_page_loaded += $wspo_page_performance[$key]['total_loaded'];
				
				if ($eff < 70 && !in_array(get_post_status($key), array('draft', 'auto-draft')))
					array_push($top_ineff_pages, array('load_eff' => $eff, 'name' => get_the_title($key)));	
			}
			else
			{
				$eff = 0;
			}
			
			$wspo_page_performance[$key]['total_request'] = $wspo_page_performance[$key]['total_request'] + $wspo_page_performance[$key]['total_ul_request'];
			$wspo_page_performance[$key]['total_request_css'] = $wspo_page_performance[$key]['total_request_css'] + $wspo_page_performance[$key]['total_ul_request_css'];
			$wspo_page_performance[$key]['total_request_js'] = $wspo_page_performance[$key]['total_request_js'] + $wspo_page_performance[$key]['total_ul_request_js'];
			$wspo_page_performance[$key]['total_size'] = wspo_format_byte($wspo_page_performance[$key]['total_size'] + $wspo_page_performance[$key]['total_ul_size']);
			$wspo_page_performance[$key]['total_size_css'] = wspo_format_byte($wspo_page_performance[$key]['total_size_css'] + $wspo_page_performance[$key]['total_ul_size_css']);
			$wspo_page_performance[$key]['total_size_js'] = wspo_format_byte($wspo_page_performance[$key]['total_size_js'] + $wspo_page_performance[$key]['total_ul_size_js']);
			$wspo_page_performance[$key]['total_time'] = wspo_format_time($wspo_page_performance[$key]['total_time'] + $wspo_page_performance[$key]['total_ul_time']);
			$wspo_page_performance[$key]['total_time_css'] = wspo_format_time($wspo_page_performance[$key]['total_time_css'] + $wspo_page_performance[$key]['total_ul_time_css']);
			$wspo_page_performance[$key]['total_time_js'] = wspo_format_time($wspo_page_performance[$key]['total_time_js'] + $wspo_page_performance[$key]['total_ul_time_js']);
			
			$wspo_page_performance[$key]['total_ul_size'] = wspo_format_byte($wspo_page_performance[$key]['total_ul_size']);
			$wspo_page_performance[$key]['total_ul_size_css'] = wspo_format_byte($wspo_page_performance[$key]['total_ul_size_css']);
			$wspo_page_performance[$key]['total_ul_size_js'] = wspo_format_byte($wspo_page_performance[$key]['total_ul_size_js']);
			$wspo_page_performance[$key]['total_ul_time'] = wspo_format_time($wspo_page_performance[$key]['total_ul_time']);
			$wspo_page_performance[$key]['total_ul_time_css'] = wspo_format_time($wspo_page_performance[$key]['total_ul_time_css']);
			$wspo_page_performance[$key]['total_ul_time_js'] = wspo_format_time($wspo_page_performance[$key]['total_ul_time_js']);
			
			$wspo_page_performance[$key]['load_eff'] = $eff;
			
			$plugin_used_str = '';
			$plugin_unused_str = '';
			$plugin_total_str = '';
			$first = true;
			foreach ($wspo_page_performance[$key]['used_plugins'] as $plugin)
			{
				$plugin_used_str .= ($first == true ? '' : ',<br/>') . $plugin['name'];
				$plugin_total_str .= ($first == true ? '' : ',<br/>') . $plugin['name'];
				$first = false;
			}
			$first = true;
			foreach ($wspo_page_performance[$key]['unused_plugins'] as $plugin)
			{
				$plugin_unused_str .= ($first == true ? '' : ',<br/>') . $plugin['name'];
				$plugin_total_str .= ($first == true ? '' : ',<br/>') . $plugin['name'];
				$first = false;
			}
			
			$wspo_page_performance[$key]['plugin_used_str'] = $plugin_used_str;
			$wspo_page_performance[$key]['plugin_unused_str'] = $plugin_unused_str;
			$wspo_page_performance[$key]['plugin_total_str'] = $plugin_total_str;
		}
		
		if ($total_page_eff_count > 0)
		{
			$avg_time = $avg_time / $total_page_eff_count;
			$avg_page_unused = round($avg_page_unused / $total_page_eff_count, 2);
			$avg_page_used = round($avg_page_used / $total_page_eff_count, 2);
			$avg_page_loaded = round($avg_page_loaded / $total_page_eff_count, 2);
			$total_page_eff = round($avg_page_used / $avg_page_loaded * 100, 2);//round($total_page_eff / $total_page_eff_count, 2);
		}
		$count_page_rules = count($wspo_page_performance);
		$page_avg_size = $total_page_eff_count > 0 ? $page_avg_size / $total_page_eff_count : 0;
		$page_avg_saved_size = $total_page_eff_count > 0 ? $page_avg_saved_size / $total_page_eff_count : 0;
		
		$avg_save_time = $count_rules > 0 ? ($total_time_js + $total_time_css) / $count_rules : 0;
		
		usort($top_ineff_pages, function($a, $b)
		{
			if ($a['load_eff'] == $b['load_eff'])
				return 0;
			return $a['load_eff'] < $b['load_eff'] ? -1 : 1;
		});
		array_splice($top_ineff_pages, 3);
		
		//Monitoring
		$wspo_monitoring['runtime_file_type'] = array(
			array('CSS', array( 'v' => $pl_css_runtime, 'f' => wspo_format_time($pl_css_runtime))),
			array('JS', array( 'v' => $pl_js_runtime, 'f' => wspo_format_time($pl_js_runtime)))
		);
		
		$wspo_monitoring['runtime_summary'] = array(
			array('Saved', array( 'v' => $page_avg_saved_size, 'f' => wspo_format_byte($page_avg_saved_size))),
			array('Unsaved', array( 'v' => $page_avg_size, 'f' =>  wspo_format_byte($page_avg_size)))
		);
		
		usort($wspo_monitoring['runtime_plugin'], function($a, $b)
		{
			if ($a[1]['v'] == $b[1]['v'])
				return 0;
			return $a[1]['v'] < $b[1]['v'] ? -1 : 1;
		});
		
		$wspo_monitoring['runtime_plugin_light'] = $wspo_monitoring['runtime_plugin'];
		usort($wspo_monitoring['runtime_plugin_light'], function($a, $b)
		{
			if ($a[1]['v'] == $b[1]['v'])
				return 0;
			return $a[1]['v'] > $b[1]['v'] ? -1 : 1;
		});
		
		array_splice($wspo_monitoring['runtime_plugin_light'], 10);
		
		$data = array(
			'general' => array(
				'count_rules' => $count_rules,
				'total_eff' => $total_eff,
				'avg_used' => $avg_used,
				'avg_unused' => $avg_unused,
				'avg_loaded' => $avg_loaded,
				'total_page_eff' => $total_page_eff,
				'avg_page_used' => $avg_page_used,
				'avg_page_unused' => $avg_page_unused,
				'avg_page_loaded' => $avg_page_loaded,
				'top_ineff_plugins' => $top_ineff_plugins,
				'top_ineff_pages' => $top_ineff_pages,
				'top_rules_size' => $top_rules_size,
				'top_rules_requests' => $top_rules_requests,
				'total_save_size' => wspo_format_byte($total_size_js + $total_size_css),
				'avg_save_size' => $count_rules > 0 ? wspo_format_byte(($total_size_js + $total_size_css) / $count_rules) : 0,
				'total_save_requests' => $total_requests_js + $total_requests_css,
				'avg_save_requests' => ($count_rules > 0 ? round(($total_requests_js + $total_requests_css) / $count_rules) : '-'),
				'total_save_time' => wspo_format_time($total_time_js + $total_time_css),
				'avg_save_time' => $count_rules > 0 ? wspo_format_time($avg_save_time) : '-',
				'avg_time' => $avg_time
			),
			'monitoring' => $wspo_monitoring,
			'rules' => $wspo_rule_performance,
			'plugins' => $wspo_plugin_performance,
			'pages' => $wspo_page_performance
		);
		ob_end_clean();
		wp_send_json(array(
				'success' => true,
				'data' => $data
			));
	}
	else
	{
		wp_send_json(array(
				'success' => false
			));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_lazy_performance_data', 'wspo_lazy_performance_data');

function wspo_save_settings_general() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-save-settings');
		
		global $wspo_data;
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);

		if (isset($params['add_bootstrap']))
		{
			$wspo_data['add_bootstrap'] = true;
		}
		else
		{
			unset($wspo_data['add_bootstrap']);
		}
		
		if (isset($params['add_fontawesome']))
		{
			$wspo_data['add_fontawesome'] = true;
		}
		else
		{
			unset($wspo_data['add_fontawesome']);
		}
		
		if (isset($params['add_bootstrap_select']))
		{
			$wspo_data['add_bootstrap_select'] = true;
		}
		else
		{
			unset($wspo_data['add_bootstrap_select']);
		}
		
		if (isset($params['add_bootstrap_toggle']))
		{
			$wspo_data['add_bootstrap_toggle'] = true;
		}
		else
		{
			unset($wspo_data['add_bootstrap_toggle']);
		}
		
		if (isset($params['add_load_chart']))
		{
			$wspo_data['add_load_chart'] = true;
		}
		else
		{
			unset($wspo_data['add_load_chart']);
		}
		
		if (isset($params['add_google_chart']))
		{
			$wspo_data['add_google_chart'] = true;
		}
		else
		{
			unset($wspo_data['add_google_chart']);
		}
		
		if (isset($params['system_plugins']) && !empty($params['system_plugins']))
		{
			$wspo_data['system_plugins'] = implode(',', $params['system_plugins']);
		}
		else
		{
			unset($wspo_data['system_plugins']);
		}
		
		if (isset($params['activate_dashboard_widget']))
		{
			$wspo_data['activate_dashboard_widget'] = true;
		}
		else
		{
			unset($wspo_data['activate_dashboard_widget']);
		}
		
		update_option('wspo_init', $wspo_data);
		
		wp_send_json(array(
				'success' => true,
				'msg' => 'Saved'
			));
	}
	else
	{
		wp_send_json(array(
				'success' => false
			));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_save_settings_general', 'wspo_save_settings_general');

function wspo_create_plugin_region() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-create-plugin-region');
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		$name = sanitize_text_field($params['name']);
		
		if ($name)
		{
			global $wpdb;
			
			$wpdb->insert('wspo_plugin_regions', array('name' => $name));
			
			$id = $wpdb->insert_id;
		}
	}
	
	if (isset($id) && $id)
	{
		wp_send_json(array(
			'success' => true,
		));
	}
	else
	{
		wp_send_json(array(
			'success' => false,
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_create_plugin_region', 'wspo_create_plugin_region');

function wspo_delete_plugin_region() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-delete-plugin-region');
		
		global $wpdb;
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		$region_id = intval(sanitize_text_field($params['region_id']));
		
		if ($region_id)
		{
			$wpdb->delete('wspo_plugin_regions', array('id' => $region_id));
			
			if ($params['move_rules'])
				$wpdb->update('wspo_plugin_rules', array('region_id' => '-1'), array('region_id' => $region_id));
			else
				$wpdb->delete('wspo_plugin_rules', array('region_id' => $region_id));
		}
		
		wp_send_json(array(
				'success' => true,
			));
	}
	else
	{
		wp_send_json(array(
				'success' => false,
			));
	}
	wp_die();
}
add_action('wp_ajax_wspo_delete_plugin_region', 'wspo_delete_plugin_region');

function wspo_update_region_name() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-update-region-name');
		
		global $wpdb;
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		$region_id = intval(sanitize_text_field($params['region_id']));
		
		if ($region_id)
		{
			$wpdb->update('wspo_plugin_regions', array('name' => sanitize_text_field($params['name'])), array('id' => $region_id));
		}
		
		wp_send_json(array(
				'success' => true,
			));
	}
	else
	{
		wp_send_json(array(
				'success' => false,
			));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_update_region_name', 'wspo_update_region_name');

function wspo_add_region_rule() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-add-region-rule');
		
		global $wpdb;
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		
		$res = false;
		if ($params['plugins'])
		{
			$region_id = $params['region_id'] ? sanitize_text_field($params['region_id']) : '-1';
			$rule_id = $params['rule_id'] ? sanitize_text_field($params['rule_id']) : '-1';
			
			$roles = implode(',', $params['user_role']);
			$plugins = implode(',', $params['plugins']);
			
			if ($rule_id && $rule_id != '-1')
			{
				$wpdb->update( 
					'wspo_plugin_rules', 
					array(
						'roles' => $roles,
						'plugins' => $plugins
						),
					array('id' => $rule_id)
				);
				$res = true;
			}
			else
			{
				$rules = $wpdb->get_results("SELECT * FROM wspo_plugin_rules");
				
				foreach ($rules as $rule)
				{
					if ($rule->type == '3')
					{
						$group = $wpdb->get_row('SELECT * FROM wspo_page_groups WHERE id = ' . $rule->arg);
						
						$rules_count += count(explode(',', $group->pages));
					}
					else
					{
						$rules_count++;
					}
				}
				
				if ($params['type'] == '0')
					$rules_count += count($params['pages']);
				if ($params['type'] == '2')
					$rules_count ++;
				if ($params['type'] == '3')
				{
					foreach ($params['groups'] as $group)
					{
						$group2 = $wpdb->get_row('SELECT * FROM wspo_page_groups WHERE id = ' . sanitize_text_field($group));
						
						$rules_count += count(explode(',', $group2->pages));
					}
				}

				if ($rules_count < 100)
				{
					if ($params['type'] == '0')
					{
						foreach ($params['pages'] as $page)
						{
							$wpdb->insert( 
								'wspo_plugin_rules', 
								array( 
									'type' => 0,
									'region_id' => $region_id,
									'arg' => sanitize_text_field($page),
									'roles' => $roles,
									'plugins' => $plugins
								)
							);
							$res = true;
						}
					}
					
					if ($params['type'] == '2')
					{
						$wpdb->insert( 
							'wspo_plugin_rules', 
							array( 
								'type' => 2,
								'region_id' => $region_id,
								'arg' => sanitize_text_field($params['regex_base']) . ',' . sanitize_text_field($params['regex_type']) . ',' . sanitize_text_field($params['regex_url']),
								'roles' => $roles,
								'plugins' => $plugins
							)
						);
						$res = true;
					}
					
					if ($params['type'] == '3')
					{
						foreach ($params['groups'] as $group)
						{
							$wpdb->insert( 
								'wspo_plugin_rules', 
								array( 
									'type' => 3,
									'region_id' => $region_id,
									'arg' => sanitize_text_field($group),
									'roles' => $roles,
									'plugins' => $plugins
								)
							);
							$res = true;
						}
					}
				}
				else
				{
					wp_send_json(array(
						'success' => false,
						'pro' => true
					));
					$pro = true;
				}
			}
		}
		
		if ($res == true)
		{
			wp_send_json(array(
				'success' => true,
				'region' => $region_id
			));
		}
		else if (!isset($pro))
		{
			wp_send_json(array(
				'success' => false,
			));
		}
	}
	else
	{
		wp_send_json(array(
			'success' => false,
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_add_region_rule', 'wspo_add_region_rule');

function wspo_delete_plugin_region_rule() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-delete-plugin-region-rule');
		
		global $wpdb;
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		
		$ids = explode(',', sanitize_text_field($params['rule_id']));
		
		foreach ($ids as $id)
		{
			$wpdb->delete( 
				'wspo_plugin_rules', 
				array( 
					'id' => $id,
				)
			);
		}
		
		wp_send_json(array(
			'success' => true
		));
	}
	else
	{
		wp_send_json(array(
			'success' => false
		));
	}
	wp_die();
}
add_action('wp_ajax_wspo_delete_plugin_region_rule', 'wspo_delete_plugin_region_rule');

function wspo_start_auto_scan() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-start-auto-scan');
		
		ob_start();
		global $wpdb;
		
		$type = sanitize_text_field($_REQUEST['type']);
		
		if ($type == '0')
		{
			$data = $_REQUEST['form_data'];
			$params = array();
			parse_str($data, $params);
			
			$plugins = $params['plugins'];
			
			$succ = false;
			
			if ($plugins && !empty($plugins))
			{
				$post_types = $params['post_types'];

				if ($post_types && !empty($post_types))
				{
					$res = array(
						'post_types' => array(),
						'count' => 0
						);
					$count_o = 0;
					foreach ($post_types as $k => $post_type)
					{
						$post_types[$k] = $post_type = sanitize_text_field($post_type);
						$query = wspo_batch_page_loop($post_type, function ($post) {});
						
						if ($query->found_posts > 0)
						{
							$count_o += $query->found_posts;
							$obj = get_post_type_object($post_type);
							
							array_push($res['post_types'], array(
								'count' => $query->found_posts,
								'ids' => $query->posts,
								'key' => $post_type,
								'name' => $obj->name,
								'label' => $obj->label
							));
						}
					}
					
					$res['count'] = count($res['post_types']);
					$res['count_o'] = $count_o;
					
					foreach ($plugins as $k => $plugin)
					{
						$plugins[$k] = $plugin = sanitize_text_field($plugin);
						wspo_update_plugin_register(sanitize_text_field($plugin));
					}
					
					$wpdb->insert( 
						'wspo_scans_auto', 
						array( 
							'type' => sanitize_text_field($params['type']),
							'post_types' => implode(',', $post_types),
							'plugins' => implode(',', $plugins)
						)
					);
					
					$succ = true;
				}
			}
			
		}
		else if ($type == '1' || $type == '2')
		{
			if ($type == '1')
			{
				$plugins = $_REQUEST['plugins'];
				$file = $_FILES["file"];

				if ($file && $file["tmp_name"])
				{
					$result = array();
					$handle = fopen($file["tmp_name"], "r");
					if (empty($handle) === false)
					{
						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
						{
							$result[] = $data;
						}
						fclose($handle);
					}
						
					$urls = array();
					
					foreach ($result as $line)
					{
						if (count($line) == 1)
						{
							array_push($urls, esc_url($line[0]));
						}
					}
				}
			}
			else if ($type == '2')
			{
				$type = '1';
				$data = $_REQUEST['form_data'];
				$params = array();
				parse_str($data, $params);
				
				$plugins = $params['plugins'];
				$urls = array();
				array_push($urls, esc_url($params['url']));
			}
				
			$res = array(
				'urls' => $urls,
				'count' => count($urls),
				'count_o' => count($urls)
				);
				
			foreach ($plugins as $k => $plugin)
			{
				$plugins[$k] = $plugin = sanitize_text_field($plugin);
				wspo_update_plugin_register(sanitize_text_field($plugin));
			}
			
			$wpdb->insert( 
				'wspo_scans_auto', 
				array( 
					'type' => $type,
					'post_types' => implode(',', $urls),
					'plugins' => implode(',', $plugins)
				)
			);
			
			$succ = true;
		}
		
		ob_end_clean();
	}
	
	if ($succ == true)
	{
		wp_send_json(array(
			'success' => true,
			'data' => $res,
			'scan_id' => $wpdb->insert_id,
			'type' => $type
		));
	}
	else
	{
		wp_send_json(array(
			'success' => false,
			'msg' => 'Invalid parameters'
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_start_auto_scan', 'wspo_start_auto_scan');

function wspo_auto_scan() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-auto-scan');
		
		ob_start();
		global $wpdb, $wspo_data;
		
		$scan_id = intval(sanitize_text_field($_REQUEST['scan_id']));
		
		if ($scan_id)
		{
			$scan = $wpdb->get_row('SELECT * FROM wspo_scans_auto WHERE id = ' . $scan_id);
			$user_roles = explode(',', $scan->user_roles);
			$plugins = explode(',', $scan->plugins);
			
			$standard_html = wspo_get_standard_html();
			
			if ($scan->type == '0')
			{
				$id = intval(sanitize_text_field($_REQUEST['arg']));
				
				$post = get_post($id);
				$url = get_permalink($post->ID);
			}
			else if ($scan->type == '1')
			{
				$url = esc_url($_REQUEST['arg']);
				$title = $url;
			}
			
			$process = curl_init();
			
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($process, CURLOPT_HEADER, false);
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			
			add_option('wspo_scan_running', true);
			$result = curl_exec($process);
			delete_option('wspo_scan_running');
			
			$info = curl_getinfo($process);
			curl_close($process);
			
			$doc = new DOMDocument();
			@$doc->loadHTML($result);

			$http = (!empty($_SERVER['HTTPS']) ? 'https:' : 'http:');
			$total_css = 0;
			$tags_links = array();
			$tags = $doc->getElementsByTagName('link');
			foreach ($tags as $tag)
			{
				$rel = $tag->getAttribute('rel');
				$href = $tag->getAttribute('href');
				if ($rel == 'stylesheet' && $href)
				{
					if (substr($href, 0, 2) == '//')
					{
						$href = $http . $href;
					}
					$href = wspo_clean_link($href);
					$total_css++;
					array_push($tags_links, explode('?', $href)[0]);
				}
			}
			
			$total_js = 0;
			$tags_scripts = array();
			$tags = $doc->getElementsByTagName('script');
			foreach ($tags as $tag)
			{
				$src = $tag->getAttribute('src');
				if ($src)
				{
					if (substr($src, 0, 2) == '//')
					{
						$src = $http . $src;
					}
					$src = wspo_clean_link($src);
					$total_js++;
					array_push($tags_scripts, explode('?', $src)[0]);
				}
			}
			
			$total_img = 0;
			$tags = $doc->getElementsByTagName('img');
			foreach ($tags as $tag)
			{
				$src = $tag->getAttribute('src');
				if ($src)
				{
					$total_img++;
				}
			}
			
			$data = array(
				'scan_data' => array(),
				'estimated' => array()
			);
			
			$standard_dom = new SelectorDOM($standard_html);
			$dom = new SelectorDOM($result);
			$system_plugins = (isset($wspo_data['system_plugins']) && $wspo_data['system_plugins'] ? explode(',', $wspo_data['system_plugins']) : array());
			
			foreach ($plugins as $plugin)
			{
				if (in_array($plugin, $system_plugins))
					continue;
				
				$plugin_reg = $wpdb->get_row('SELECT * FROM wspo_plugin_register WHERE plugin = "' . $plugin . '"');
				$css_files = json_decode($plugin_reg->css_files);
				$js_files = json_decode($plugin_reg->js_files);
				
				$used = false;
				$sele = array();
				$count_css = 0;
				$size_css = 0;
				$time_css = 0;
				foreach ($css_files as $css)
				{
					if (in_array($css->url, $tags_links))
					{
						$count_css++;
						
						foreach ($css->selectors as $sel)
						{
							if ($standard_dom->select($sel, false)->length == 0)
							{
								$t_el = $dom->select($sel, false);
								$has_visible = false;
								for ($i = 0, $length = $t_el->length; $i < $length; ++$i)
								{
									if (!wspo_dom_is_invisible($t_el->item($i)))
									{
										$has_visible = true;//array_push($el, $t_el->item($i));
										break;
									}
								}
								
								if ($has_visible)// && !in_array($plugin, $used_plugins))
								{
									array_push($sele, $sel);
									$used = true;
									break;
								}
							}
						}
						$filedata = wspo_get_remote_file_data($css->url, 'css');
						if ($filedata['res'])
						{
							$size = $filedata['data']['size'] ? $filedata['data']['size'] : 0;
							$time = $filedata['data']['time'] ? $filedata['data']['time'] : 0;
							$size_css += $size;
							$time_css += $time;
						}
						/*
						$size = filesize($css->path);
						if ($size)
							$size_css += $size;*/
					}
				}
				
				$count_js = 0;
				$size_js = 0;
				$time_js = 0;
				foreach ($js_files as $js)
				{
					if (in_array($js->url, $tags_scripts))
					{
						$count_js++;
						$filedata = wspo_get_remote_file_data($js->url, 'js');
						if ($filedata['res'])
						{
							$size = $filedata['data']['size'] ? $filedata['data']['size'] : 0;
							$time = $filedata['data']['time'] ? $filedata['data']['time'] : 0;
							$size_js += $size;
							$time_js += $time;
						}
						/*$size = filesize($js->path);
						if ($size)
							$size_js += $size;*/
					}
				}
				
				if (!$used && $count_css == 0 && $count_js > 0)
					$used = true;
				
				$needs_rule = ($count_css == 0 && $count_js == 0) ? false : ($used ? false : true);
				
				$data['scan_data'][$plugin] = array(
					'used' => $used,
					'needs_rule' => $needs_rule,
					'add_rule' => $needs_rule,
					'css_files' => $count_css,
					'css_size' => $size_css,
					'css_time' => $time_css,
					'js_files' => $count_js,
					'js_size' => $size_js,
					'js_time' => $time_js,
					'selectors' => $sele
				);
			}
			
			$save_css = 0;
			$save_js = 0;
			$save_css_size = 0;
			$save_js_size = 0;
			$save_css_time = 0;
			$save_js_time = 0;
			
			$est_css = 0;
			$est_js = 0;
			$est_css_size = 0;
			$est_js_size = 0;
			$est_css_time = 0;
			$est_js_time = 0;
			
			foreach ($data['scan_data'] as $pl)
			{
				if ($pl['needs_rule'])
				{
					$save_css += $pl['css_files'];
					$save_js += $pl['js_files'];
					$save_css_size += $pl['css_size'];
					$save_js_size += $pl['js_size'];
					$save_css_time += $pl['css_time'];
					$save_js_time += $pl['js_time'];
				}
				
				$est_css += $pl['css_files'];
				$est_js += $pl['js_files'];
				$est_css_size += $pl['css_size'];
				$est_js_size += $pl['js_size'];
				$est_css_time += $pl['css_time'];
				$est_js_time += $pl['js_time'];
			}
			
			$data['estimated'] = array(
				'save_request' => $save_css + $save_js,
				'save_css' => $save_css,
				'save_js' => $save_js,
				'save_size' => $save_css_size + $save_js_size,
				'save_size_f' => wspo_format_byte($save_css_size + $save_js_size),
				'save_css_size' => $save_css_size,
				'save_js_size' => $save_js_size,
				'save_time' => $save_css_time + $save_js_time,
				'save_time_f' => wspo_format_time($save_css_time + $save_js_time),
				'save_css_time' => $save_css_time,
				'save_js_time' => $save_js_time,
				'total_request' => $est_css + $est_js,
				'total_css' => $est_css,
				'total_js' => $est_js,
				'total_size' => $est_css_size + $est_js_size,
				'total_size_f' => wspo_format_byte($est_css_size + $est_js_size),
				'total_css_size' => $est_css_size,
				'total_js_size' => $est_js_size,
				'total_time' => $est_css_time + $est_js_time,
				'total_time_f' => wspo_format_time($est_css_time + $est_js_time),
				'total_css_time' => $est_css_time,
				'total_js_time' => $est_js_time
			);
			
			$wpdb->insert( 
				'wspo_scans_auto_data', 
				array( 
					'arg' => ($scan->type == '0' ? $post->ID . ',' . $post->post_type : ($scan->type == '1' ? $url : '')),
					'data' => json_encode($data),
					'scan_id' => $scan_id
				)
			);
			
			ob_end_clean();
			wp_send_json(array(
				'success' => true,
				'title' => ($scan->type == '0' ? $post->post_title : ($scan->type == '1' ? $url : '')),
				'data' => $data,
			));
			
			$succ = true;
		}
	}
	
	if ($succ == false)
	{
		ob_end_clean();
		wp_send_json(array(
			'success' => false,
			'msg' => 'Scan Error'
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_auto_scan', 'wspo_auto_scan');

function wspo_finish_scan() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-auto-scan-finish');
		
		global $wpdb;
		$scan_id = intval(sanitize_text_field($_REQUEST['scan_id']));
		
		if ($scan_id)
		{
			foreach (glob($wspo_plugin_path . '/temp/*.*') as $v)
			{
				unlink($v);
			}
			
			$scan_results = $wpdb->get_results( 
					"
					SELECT *
					FROM wspo_scans_auto_data
					WHERE scan_id = " . $scan_id
				);
			$result_count = count($scan_results);
				
			$total_saved_css = 0;
			$total_saved_js = 0;
			$total_saved_css_size = 0;
			$total_saved_js_size = 0;
			$total_saved_css_time = 0;
			$total_saved_js_time = 0;
			
			foreach ($scan_results as $result)
			{
				$data = json_decode($result->data);
				
				$total_saved_css += $data->estimated->save_css;
				$total_saved_js += $data->estimated->save_js;
				$total_saved_css_size += $data->estimated->save_css_size;
				$total_saved_js_size += $data->estimated->save_js_size;
				$total_saved_css_time += $data->estimated->save_css_time;
				$total_saved_js_time += $data->estimated->save_js_time;
			}
			
			$avg_saved_css = $total_saved_css / $result_count;
			$avg_saved_js = $total_saved_js / $result_count;
			$avg_saved_css_size = $total_saved_css_size / $result_count;
			$avg_saved_js_size = $total_saved_js_size / $result_count;
			$avg_saved_css_time = $total_saved_css_time / $result_count;
			$avg_saved_js_time = $total_saved_js_time / $result_count;
			
			$wpdb->update('wspo_scans_auto', 
				array(
					'finished' => 1,
					'estimated' => json_encode(array(
						'total_saved_css' => $total_saved_css,
						'total_saved_js' => $total_saved_js,
						'total_saved_css_size' => $total_saved_css_size,
						'total_saved_js_size' => $total_saved_js_size,
						'total_saved_css_time' => $total_saved_css_time,
						'total_saved_js_time' => $total_saved_js_time,
						'avg_saved_css' => $avg_saved_css,
						'avg_saved_js' => $avg_saved_js,
						'avg_saved_css_size' => $avg_saved_css_size,
						'avg_saved_js_size' => $avg_saved_js_size,
						'avg_saved_css_time' => $avg_saved_css_time,
						'avg_saved_js_time' => $avg_saved_js_time,
					))),
				array('id' => $scan_id)
				);
		}
		
		wp_send_json(array(
				'success' => true,
			));
	}
	else
	{
		wp_send_json(array(
			'success' => false,
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_finish_scan', 'wspo_finish_scan');

function wspo_delete_scan() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-delete-scan');
		
		global $wpdb;
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		$scan_ids = explode(',', sanitize_text_field($params['scan_id']));
		
		foreach ($scan_ids as $scan_id)
		{
			$region_id = $wpdb->get_var('SELECT region_id FROM wspo_scans_auto WHERE id=' . $scan_id);
			$wpdb->delete('wspo_scans_auto_data', array('scan_id' => $scan_id));
			$wpdb->delete('wspo_scans_auto', array('id' => $scan_id));
			if ($region_id && $region_id != '-1' && $params['clear_all'])
			{
				$wpdb->delete('wspo_plugin_regions', array('id' => $region_id));
				$wpdb->delete('wspo_plugin_rules', array('region_id' => $region_id));
			}
		}
			
		wp_send_json(array(
				'success' => true,
			));
	}
	else
	{
		wp_send_json(array(
				'success' => true,
			));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_delete_scan', 'wspo_delete_scan');

function wspo_edit_scan_rule() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-edit-scan-rule');
		
		global $wpdb;
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		
		$rule_id = intval(sanitize_text_field($params['rule_id']));
		$plugins = $params['plugins'];
		
		$res = false;
		
		if ($rule_id && $rule_id != '-1')
		{
			$rule = $wpdb->get_row( "SELECT * FROM wspo_scans_auto_data WHERE id = " . $rule_id );
			
			if ($rule)
			{
				$data = json_decode($rule->data);
				
				foreach ($data->scan_data as $key => $scan_data)
				{
					if (in_array($key, $plugins))
						$scan_data->add_rule = true;
					else
						$scan_data->add_rule = false;
				}
				
				$wpdb->update('wspo_scans_auto_data', array( 'data' => json_encode($data)), array('id' => $rule_id));
				$res = true;
			}
		}
	}
	
	if ($res)
	{
		wp_send_json(array(
				'success' => true,
				'scan_id' => $rule_id
			));
	}
	else
	{
		wp_send_json(array(
				'success' => false,
			));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_edit_scan_rule', 'wspo_edit_scan_rule');

function wspo_create_scan_region() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-create-scan-region');
		
		global $wpdb;
		$scan_id = intval(sanitize_text_field($_REQUEST['scan_id']));
		$execute = explode(',', sanitize_text_field($_REQUEST['execute']));
		
		$res = false;
		$pro = false;
		if ($scan_id)
		{
			$scan = $wpdb->get_row( "SELECT * FROM wspo_scans_auto WHERE id = " . $scan_id );
			
			if ($scan)
			{
				$rules = $wpdb->get_results("SELECT * FROM wspo_plugin_rules");
				$rules_count = 0;
				foreach ($rules as $rule)
				{
					if ($rule->type == '3')
					{
						$group = $wpdb->get_row('SELECT * FROM wspo_page_groups WHERE id = ' . $rule->arg);
						$rules_count += count(explode(',', $group->pages));
					}
					else
					{
						$rules_count++;
					}
				}
				
				$scan_results = $wpdb->get_results( 
						"
						SELECT *
						FROM wspo_scans_auto_data
						WHERE scan_id = " . $scan_id
					);
					
				
				if ($rules_count + count($execute) < 100)
				{
					$wpdb->insert('wspo_plugin_regions', array('name' => 'Scan from ' . $scan->timestamp));
					$region_id = $wpdb->insert_id;
					
					$wpdb->update('wspo_scans_auto', array('region_id' => $region_id), array('id' => $scan_id));
					
					foreach ($scan_results as $scan_result)
					{
						if (in_array($scan_result->id, $execute))
						{
							$data = json_decode($scan_result->data);
							
							$plugins = array();
							
							foreach ($data->scan_data as $key => $result)
							{
								if ($result->add_rule)
								{
									array_push($plugins, $key);
								}
							}
							
							if (!empty($plugins))
							{
								$rules_count++;
								$wpdb->insert( 
									'wspo_plugin_rules', 
									array( 
										'type' => ($scan->type == '0' ? 0 : ($scan->type == '1' ? 2 : -1)),
										'region_id' => $region_id,
										'arg' => ($scan->type == '0' ? explode(',', $scan_result->arg)[0] : ($scan->type == '1' ? '0,0,' . $scan_result->arg : '')),
										'roles' => '-1',
										'plugins' => implode(',', $plugins)
									)
								);
							}
						}
					}
					$res = true;
				}
				else
				{
					$pro = true;
				}
			}
		}
	
		if ($res && $pro == false)
		{
			wp_send_json(array(
					'success' => true,
					'scan_id' => $scan_id
				));
		}
		else
		{
			wp_send_json(array(
					'success' => false,
					'pro' => $pro,
					'left' => 100 - $rules_count
				));
		}
	}
	else
	{
		wp_send_json(array(
				'success' => false,
			));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_create_scan_region', 'wspo_create_scan_region');

function wspo_add_plugin_group() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-add-plugin-group');
		
		global $wpdb;
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		
		$group_id = $params['group_id'] != '-1' ? intval(sanitize_text_field($params['group_id'])) : '';
		
		$plugins = sanitize_text_field(implode(',', $params['plugins']));
		$name = sanitize_text_field($params['name']);
		if ($group_id && $group_id != '')
		{
			$wpdb->update( 
				'wspo_plugin_groups', 
				array(
					'name' => $name,
					'plugins' => $plugins
					),
				array('id' => $group_id)
			);
			
			wp_send_json(array(
				'success' => true,
			));
		}
		else
		{	
			$wpdb->insert('wspo_plugin_groups', array('name' => $name, 'plugins' => $plugins));
			$id = $wpdb->insert_id;
			
			if ($id)
			{
				wp_send_json(array(
					'success' => true,
				));
			}
			else
			{
				wp_send_json(array(
					'success' => false,
				));
			}	
		}
	}
	else
	{
		wp_send_json(array(
			'success' => false,
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_add_plugin_group', 'wspo_add_plugin_group');

function wspo_add_page_group() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-add-page-group');
		
		global $wpdb;
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		
		$group_id = $params['group_id'] != '-1' ? intval(sanitize_text_field($params['group_id'])) : '';
		
		$pages = sanitize_text_field(implode(',', $params['pages']));
		$name = sanitize_text_field($params['name']);
		if ($group_id && $group_id != '')
		{
			$rules = $wpdb->get_results("SELECT * FROM wspo_plugin_rules");
			$rules_count = 0;
			foreach ($rules as $rule)
			{
				if ($rule->type == '3')
				{
					if ($rule->arg == $group_id)
					{
						$rules_count += count(explode(',', $pages));
					}
					else
					{
						$group = $wpdb->get_row('SELECT * FROM wspo_page_groups WHERE id = ' . $rule->arg);
						$rules_count += count(explode(',', $group->pages));
					}
				}
				else
				{
					$rules_count++;
				}
			}
			
			if ($rules_count < 100)
			{
				$wpdb->update( 
					'wspo_page_groups', 
					array(
						'name' => $name,
						'pages' => $pages
						),
					array('id' => $group_id)
				);
				
				wp_send_json(array(
					'success' => true,
				));
			}
			else
			{
				wp_send_json(array(
					'success' => false,
					'pro' => true,
				));
			}
			
		}
		else
		{	
			$wpdb->insert('wspo_page_groups', array( 'name' => $name, 'pages' => $pages));
			$id = $wpdb->insert_id;
			
			if ($id)
			{
				wp_send_json(array(
					'success' => true,
				));
			}
			else
			{
				wp_send_json(array(
					'success' => false,
				));
			}	
		}
	}
	else
	{
		wp_send_json(array(
			'success' => false,
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_add_page_group', 'wspo_add_page_group');

function wspo_delete_group() //-
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-delete-group');
		
		global $wpdb;
		
		$data = $_REQUEST['form_data'];
		$params = array();
		parse_str($data, $params);
		
		$group_id = $params['group_id'] != '-1' ? intval(sanitize_text_field($params['group_id'])) : '';
		$succ = false;
		
		if ($group_id)
		{
			if ($params['group'] == 'page')
			{
				$wpdb->delete('wspo_page_groups', array( 'id' => $group_id));
				
				wp_send_json(array(
					'success' => true,
				));
				$succ = true;
			}
			
			if ($params['group'] == 'plugin')
			{
				$wpdb->delete('wspo_plugin_groups', array( 'id' => $group_id));
				
				wp_send_json(array(
					'success' => true,
				));
				$succ = true;
			}
		}
	}
	
	if ($succ == false)
	{
		wp_send_json(array(
			'success' => false,
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_delete_group', 'wspo_delete_group');

function wspo_update_register()
{
	if (current_user_can('manage_options'))
	{
		wp_verify_nonce($_REQUEST['nonce'], 'wspo-update-cache');
		
		wspo_update_page_register();
		
		wp_send_json(array(
			'success' => true,
			'msg' => 'Page Register updated!'
		));
	}
	else
	{
		wp_send_json(array(
			'success' => false,
		));
	}
	
	wp_die();
}
add_action('wp_ajax_wspo_update_register', 'wspo_update_register');
?>