<?php
if (!is_admin())
{
	$data = get_option('wsko_init');
	$path = $data && isset($data['plugin_path']) ? $data['plugin_path'] : false; 
	if ($path && file_exists($path . '/wspo-mu.php'))
		require ($path . '/wspo-mu.php');
}
?>