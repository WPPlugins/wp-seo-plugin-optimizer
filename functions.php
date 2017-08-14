<?php
function wspo_clean_link($link)
{
	$clean = '';
	
	$parts = explode('//', $link);
	
	if (count($parts) > 2)
	{
		$clean .= $parts[0] . '//' . $parts[1];
		unset($parts[0]);
		unset($parts[1]);
		foreach ($parts as $part)
		{
			$clean .= '/' . $part;
		}
	}
	else
	{
		$clean = $link;
	}
	
	return $clean;
}

function wspo_get_remote_file_data($url, $type)
{
	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	//curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_ENCODING , "gzip deflate identity");
	curl_setopt($curl, CURLOPT_TIMEOUT_MS, 2000);
	curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11");
	$result = curl_exec($curl);
	
	$res = array('res' => false);
	if ($result === false)
	{
		$res['error'] = curl_error($curl);
		curl_close($curl);
	}
	else
	{
		$res['res'] = true;
		$res['data'] = array(
			'size' => curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD),
			'time' => curl_getinfo($curl, CURLINFO_TOTAL_TIME) * (curl_getinfo($curl, CURLINFO_SPEED_DOWNLOAD) / 30000000 * 100),
			);
		curl_close($curl);
		
		if ($type == "css")
		{
			preg_match_all('/(?ims)([a-z0-9, \s\.\:#_\-@]+)\{([^\}]*)\}/', $result, $arr);
			$selectors = array();
			
			foreach ($arr[0] as $i => $x)
			{
				$selector = trim($arr[1][$i]);
				array_push($selectors, $selector);
			}
			
			$res['data']['selectors'] = $selectors;
		}
	}
	
	return $res;
}

function wspo_crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}

function wspo_get_token($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[wspo_crypto_rand_secure(0, $max)];
    }

    return $token;
}

function wspo_plugin_has_rule($page, $plugin)
{
	global $wpdb;
	
	$rules = $wpdb->get_results( 
		'
		SELECT *
		FROM wspo_plugin_rules
		WHERE type = "0"
		AND arg = "' . $page . '"'
	);
	
	foreach ($rules as $rule)
		if (in_array($plugin, explode(',', $rule->plugins)))
			return true;
		
	return false;
}

function wspo_dom_is_invisible($el)
{
	foreach ($el->attributes as $attr) {
    if ($attr->nodeName == "style")
	{
		$style = $attr->nodeValue;
		if ($style)
		{
			preg_match_all("/([\w-]+)\s*:\s*([^;]+)\s*;?/", $style, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				if (($match[1] == 'display' && $match[2] == 'none') ||
					($match[1] == 'visibility' && $match[2] == 'hidden'))
					return true;
			}		
		}
	}
  }
  
	/*if ($el->getAttribute('display') == 'none' || $el->getAttribute('visibility') == 'hidden')
		return true;*/
	$par = $el->parentNode;
	if ($par)
		return wspo_dom_is_invisible($par);
	
	return false;
}

function wspo_format_byte($byte)
{
	$ending = ' Byte';
	
	if ($byte > 1000)
	{
		$byte = $byte / 1000;
		$ending = ' KB';
	}
	
	if ($byte > 1000)
	{
		$byte = $byte / 1000;
		$ending = ' MB';
	}
	
	$byte = round($byte, 2);
	
	return $byte . $ending;
}

function wspo_format_time($time)
{
	$ending = ' s';
	
	if ($time < 1)
	{
		$time = $time * 1000;
		$ending = ' ms';
	}
	else if ($time > 60)
	{
		$time = $time / 60;
		$ending = ' min';
	}
	
	$time = round($time, 2);
	
	return $time . $ending;
}

function wspo_get_plugin_performance($wspo_scans, $plugin, $with_rule)
{
	global $wpdb;
	
	$pages_t = array();
	$pages = array();
	$l_pages = array();
	
	if (is_plugin_active($plugin))
	{
		foreach ($wspo_scans as $scan)
		{
			if ($scan->finished && $scan->type == '0')
			{
				$plugins = explode(',', $scan->plugins);
				
				$scan_results = $wpdb->get_results( 
					"
					SELECT *
					FROM wspo_scans_auto_data
					WHERE scan_id=" . $scan->id
				);
				
				if (in_array($plugin, $plugins))
				{
					foreach ($scan_results as $scan_result)
					{
						$page = explode(',', $scan_result->arg)[0];
						if (!in_array($page, $pages_t) && ($with_rule === true ? !wspo_plugin_has_rule($page, $plugin) : true))
						{
							$d_t = json_decode($scan_result->data)->scan_data;
							
							if (isset($d_t->$plugin))
							{
								$data = $d_t->$plugin;
								if ($data->used)
								{
									array_push($pages_t, $page);
										
									array_push($pages, array(
											'page' => $page,
											'data' => $data
										));
								}
								
								if ($data->css_files > 0 || $data->js_files > 0)
								{
									array_push($pages_t, $page);
										
									array_push($l_pages, array(
											'page' => $page,
											'data' => $data
										));
								}
							}
						}
					}
				}
			}
		}
	}
	
	return array(
		'used_pages' => $pages,
		'loaded_pages' => $l_pages
		);
}

function wspo_get_rule_performance($wspo_scans, $rule)
{
	global $wpdb;
	
	$canScan = false;
	$pages = array();
	
	switch ($rule->type)
	{
		case '0':
			array_push($pages, $rule->arg);
			$canScan = true;
			break;
			
		case '1':
			$args = explode(',', $rule->arg);
			
			if ($args[1] == 'single')
			{
				$pages = wspo_batch_page_loop($args[0], null, true);
				$canScan = true;
			}
			break;
			
		case '2':
			$args = explode(',', $rule->arg);
			if ($args[0] == '0' && $args[1] == '0')
			{
				array_push($pages, $args[2]);
				$canScan = true;
			}
			break;
			
		case '3':
			$group = $wpdb->get_row('SELECT * FROM wspo_page_groups WHERE id=' . $rule->arg);
			if ($group)
			{
				$t_p = explode(',', $group->pages);
				foreach ($t_p as $p)
				{
					array_push($pages, $p);
				}
				$canScan = true;
			}
			break;
	}
	
	$total_saved_css = 0;
	$total_saved_js = 0;
	$total_saved_css_size = 0;
	$total_saved_js_size = 0;
	$total_saved_css_time = 0;
	$total_saved_js_time = 0;
	
	$t_plugins = explode(',', $rule->plugins);
	$error_res = array();
	if ($canScan)
	{
		foreach ($pages as $page_key => $page)
		{
			$plugins = $t_plugins;
			
			foreach ($wspo_scans as $scan)
			{
				if ($scan->finished && !empty($plugins))
				{
					$scan_results = $wpdb->get_results( 
						"
						SELECT *
						FROM wspo_scans_auto_data
						WHERE scan_id=" . $scan->id
					);
					
					foreach ($scan_results as $scan_result)
					{
						if ($page == explode(',', $scan_result->arg)[0])
						{
							$data = json_decode($scan_result->data);
							
							foreach ($data->scan_data as $key => $result)
							{
								if (!empty($plugins))
								{
									$in_arr = array_search($key, $plugins);
									
									if ($in_arr !== false)
									{
										if (is_plugin_active($key))
										{
											$total_saved_css += $result->css_files;
											$total_saved_js += $result->js_files;
											$total_saved_css_size += $result->css_size;
											$total_saved_js_size += $result->js_size;
											$total_saved_css_time += $result->css_time;
											$total_saved_js_time += $result->js_time;
										}
										
										unset($plugins[$in_arr]);
									}
								}
							}
						}
					}
				}
			}
			
			if (empty($plugins))
			{
				unset($pages[$page_key]);
			}
			else
			{
				$error_res[(string)$page] = $plugins;
			}
		}
	}
	
	if ($canScan && empty($pages))
	{
		$args = array( 
			'res' => true,
			'total_css' => $total_saved_css,
			'total_js' => $total_saved_js,
			'total_css_size' => $total_saved_css_size,
			'total_js_size' => $total_saved_js_size,
			'total_css_time' => $total_saved_css_time,
			'total_js_time' => $total_saved_js_time
		);
	}
	else
	{
		$args = array( 
			'res' => false,
			'can_scan' => $canScan,
			'pages' => $pages,
			'error' => $error_res,
		);
	}
	
	return $args;
}

function wspo_get_standard_html()
{
	ob_start();
	include(dirname(__FILE__) . '/includes/default-html.php');
	return ob_get_clean();
}

function wspo_update_page_register()
{
	global $wpdb;
	$post_types = get_post_types('', 'objects');
	//Post Types
	foreach ($post_types as $post_type)
	{		
		$id = $wpdb->get_var($wpdb->prepare( 
			"
				SELECT id
				FROM wspo_page_register
				WHERE type = %s
				AND arg = %s
			", 
			'0',
			$post_type->name
		));
		
		$obj = get_post_type_object($post_type->name);
		
		$args = array(
						'type' => '0',
						'arg' => $post_type->name,
						'url' => $obj->rewrite ? '/' . $obj->rewrite['slug'] . '/' : '',
					);
		
		if ($id)
		{
			$wpdb->update('wspo_page_register', $args, array( 'id' => $id) );
		}
		else
		{
			$wpdb->insert('wspo_page_register', $args);
		}
		
		//Pages
		wspo_batch_page_loop($post_type->name, function ($post) {
			wspo_update_page_register_post($post);
		});
	}
}

function wspo_update_page_register_post($post)
{
	global $wpdb;
	$id = $wpdb->get_var($wpdb->prepare( 
		"
			SELECT id
			FROM wspo_page_register
			WHERE type = %s
			AND arg = %s
		", 
		'1',
		(string)$post
	));
	
	$args = array(
					'type' => '1',
					'arg' => $post,
					'url' => get_permalink($post),
				);
			
	if ($id)
	{
		$wpdb->update('wspo_page_register', $args, array( 'id' => $id) );
	}
	else
	{
		$wpdb->insert('wspo_page_register', $args);
	}
}

function wspo_get_rule_edit_button($rule, $nested)
{
	global $wpdb;
	
	switch ($rule->type)
	{
		case '0':
			$rule_title = 'Page Rule for ' . get_the_title($rule->arg);
			break;
		case '1':
			$args = explode(',', $rule->arg);
			$p_obj = get_post_type_object($args[0]);
			$rule_title = 'Post Type Rule (' . $args[1] . ') for ' . $p_obj->label;
			break;
		case '2':
			$args = explode(',', $rule->arg);
			$rule_title = 'Regex Rule (' . $args[0] . '|' . $args[1] . ') for ' . $args[2];
			break;
		case '3':
			$rule_title = 'Page Rule for Group ' . $wpdb->get_var('SELECT name FROM wspo_page_groups WHERE id=' . $rule->arg);
			break;		 
	}
	?>
	<a class="btn-edit-region-rule" href="#" <?=$nested ? '' : 'data-toggle="modal"'?> data-target="#modal_add_edit_rule" data-title="<?=$rule_title?>" data-rule="<?=$rule->id?>" data-roles="<?=$rule->roles?>" data-plugins="<?=$rule->plugins?>"><i class="fa fa-pencil"></i></a>
	<?php
}

function wspo_update_plugin_register($plugin)
{
	global $wpdb;
	$args = explode('/', $plugin);
	
	if (count($args) == 2)
	{
		$css_files = array();
		$js_files = array();
		$res = wspo_get_plugin_files(WP_PLUGIN_DIR . '/' . $args[0] . '/', $css_files, $js_files);
		$uploads_path = wp_upload_dir()['basedir'] . '/' . $args[0] . '/';
		
		if (file_exists($uploads_path))
			$res2 = wspo_get_plugin_files($uploads_path, $css_files, $js_files);
		
		$id = $wpdb->get_var('SELECT id FROM wspo_plugin_register WHERE plugin = "' . $plugin . '"');
		
		$args = array(
			'plugin' => $plugin,
			'css_files' => json_encode($css_files),
			'js_files' =>  json_encode($js_files),
		);
		
		if ($id == null)
		{
			$wpdb->insert('wspo_plugin_register', $args);
		}
		else
		{
			$wpdb->update('wspo_plugin_register', $args, array('id' => $id));
		}
	}
}

function wspo_get_plugin_files($path, &$css_files, &$js_files)
{
	$Directory = new RecursiveDirectoryIterator($path);
	$Iterator = new RecursiveIteratorIterator($Directory);
	$Regex = new RegexIterator($Iterator, '/^.+(.css)$/i', RecursiveRegexIterator::GET_MATCH);
	
	foreach ($Regex as $name => $Re)
	{
		$file = file_get_contents($name);
		
		preg_match_all( '/(?ims)([a-z0-9, \s\.\:#_\-@]+)\{([^\}]*)\}/', $file, $arr);
		$selectors = array();
		
		foreach ($arr[0] as $i => $x)
		{
			$selector = trim($arr[1][$i]);
			
			array_push($selectors, $selector);
			/*$rules = explode(';', trim($arr[2][$i]));
			
			$this->css[$selector] = array();
			foreach ($rules as $strRule)
			{
				if (!empty($strRule))
				{
					$rule = explode(":", $strRule);
					$this->css[$selector][trim($rule[0])] = trim($rule[1]);
				}
			}*/
		}
		
		array_push($css_files, array(
			'name' => str_replace($path, '', $name),
			'path' => $name,
			'url' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . ($_SERVER['CONTEXT_PREFIX'] ? $_SERVER['CONTEXT_PREFIX'] . substr($name, strlen($_SERVER['CONTEXT_DOCUMENT_ROOT'])) : substr($name, strlen($_SERVER['DOCUMENT_ROOT']))),
			'selectors' => $selectors,
		));
	}
	
	$Regex = new RegexIterator($Iterator, '/^.+(.js)$/i', RecursiveRegexIterator::GET_MATCH); // Regex: "/^.+(.jpe?g|.png)$/i"
	
	foreach ($Regex as $name => $Re)
	{
		$file = file_get_contents($name);
		$selectors = array();
		
		array_push($js_files, array(
			'name' => str_replace($path, '', $name),
			'path' => $name,
			'url' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . ($_SERVER['CONTEXT_PREFIX'] ? $_SERVER['CONTEXT_PREFIX'] . substr($name, strlen($_SERVER['CONTEXT_DOCUMENT_ROOT'])) : substr($name, strlen($_SERVER['DOCUMENT_ROOT']))),
			'selectors' => $selectors,
		));
	}
}

function wspo_batch_page_loop($post_type, $callback, $ids = false)
{
	$query = new WP_Query;
	
	$query_args = array(
		'post_type' => $post_type,
		'posts_per_page' => 500,
		'post_status' => 'any',
		'fields' => 'ids',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false
	);
	
	//if (!get_option('wspo_scan_draft'))
	//{
		$stati = get_post_stati();
		
		foreach ($stati as $key => $status)
			if ($status == 'draft' || $status == 'auto-draft')
				unset($stati[$key]);
			
		$query_args['post_status'] = $stati;
		
	//}
	
	$paged = 1;
	$count = 0;
	$total = null;

	do
	{
		$query_args['no_found_rows'] = isset($total);
		$query_args['paged'] = $paged++;

		$posts = $query->query($query_args);
		
		if (!isset($total))
			$total = $query->found_posts;

		$count += $query->post_count;
		
		if ($ids == false)
		{
			foreach ($posts as $post)
			{
				$callback($post);
			}
		}
	}
	while ($count < $total);
	
	if ($ids == true)
		return $posts;
	else
		return $query;
}
?>