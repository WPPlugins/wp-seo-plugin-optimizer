<?php
/*
Plugin Name: 	WP SEO Plugin Optimizer
Plugin URI: 	http://www.bavoko.services/wordpress/
Description: 	Deactivate unneeded Plugin Code to speed up your Website
Version: 		1.1.2
Author: 		BAVOKO
Author URI: 	http://www.bavoko.services/
License:     	GPL2 or later
*/
/*
WP SEO Plugin Optimizer is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
WP SEO Plugin Optimizer is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/
global $wspo_plugin_path, $wspo_data;
$wspo_plugin_path = dirname(__FILE__);

$wspo_data = get_option('wspo_init');

define('WSPO_VERSION', '1.1.2');

if (is_admin())
{
	if (!class_exists('SelectorDOM'))
		include(plugin_dir_path( __FILE__ ) . '/includes/selector.inc');
	
	if (!defined('DOING_AJAX') || !DOING_AJAX)
	{
		include(plugin_dir_path( __FILE__ ) . '/upgrade.php');
		include(plugin_dir_path( __FILE__ ) . '/admin/admin.php');
	}
	
	include(plugin_dir_path( __FILE__ ) . '/admin/admin-ajax.php');
}

require_once(plugin_dir_path( __FILE__ ) . '/functions.php');

function wspo_install_plugin()
{
	ob_start();
	require_once(plugin_dir_path( __FILE__ ) . '/functions.php');
	
	if(!is_dir(WPMU_PLUGIN_DIR . '/'))
		mkdir(WPMU_PLUGIN_DIR . '/');
		
	$res = copy(plugin_dir_path( __FILE__ ) . '/wspo-mu-load.php', WPMU_PLUGIN_DIR . '/wspo-mu-load.php');
	
	global $wpdb;
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE wspo_plugin_regions (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name text NOT NULL,
		timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	$sql = "CREATE TABLE wspo_plugin_rules (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		type tinyint(3) NOT NULL,
		arg text NOT NULL,
		roles text NOT NULL,
		plugins text NOT NULL,
		region_id mediumint(9) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	$sql = "CREATE TABLE wspo_scans_auto (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		type text NOT NULL,
		post_types text NOT NULL,
		plugins text NOT NULL,
		timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		finished BOOL NOT NULL DEFAULT '0',
		estimated TEXT NOT NULL DEFAULT '',
		region_id mediumint(9) NOT NULL DEFAULT -1,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	$sql = "CREATE TABLE wspo_scans_auto_data (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		arg text NOT NULL,
		data mediumtext NOT NULL,
		scan_id mediumint(9) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	$sql = "CREATE TABLE wspo_plugin_groups (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name text NOT NULL,
		plugins text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	$sql = "CREATE TABLE wspo_page_groups (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name text NOT NULL,
		pages text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	$sql = "CREATE TABLE wspo_page_register (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		type text NOT NULL,
		arg text NOT NULL,
		url text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	$sql = "CREATE TABLE wspo_plugin_register (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		plugin text NOT NULL,
		css_files mediumtext NOT NULL,
		js_files mediumtext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta( $sql );
	
	wspo_update_page_register();
	ob_end_clean();
}
register_activation_hook( __FILE__, 'wspo_install_plugin' );

function wspo_deinstall_plugin()
{
	unlink(WPMU_PLUGIN_DIR . '/wspo-mu-load.php');
}
register_deactivation_hook( __FILE__, 'wspo_deinstall_plugin' );

function wspo_insert_post_hook($post_id)
{
	wspo_update_page_register_post($post_id);
}
add_action('wp_insert_post', 'wspo_insert_post_hook');
add_action('save_post', 'wspo_insert_post_hook');

function wspo_post_transition_hook($new_status, $old_status=null, $post=null)
{
	if ($post)
		wspo_update_page_register_post($post->ID);
}
add_action('transition_post_status', 'wspo_post_transition_hook');
?>