<?php
if (!defined('WP_UNINSTALL_PLUGIN'))
{
	exit; // Exit if accessed directly
}

global $wpdb;

delete_option('wsko_init');

$wpdb->query("DROP TABLE IF EXISTS wspo_plugin_regions");
$wpdb->query("DROP TABLE IF EXISTS wspo_plugin_rules");
$wpdb->query("DROP TABLE IF EXISTS wspo_scans_auto");
$wpdb->query("DROP TABLE IF EXISTS wspo_scans_auto_data");
$wpdb->query("DROP TABLE IF EXISTS wspo_plugin_groups");
$wpdb->query("DROP TABLE IF EXISTS wspo_page_groups");
$wpdb->query("DROP TABLE IF EXISTS wspo_page_register");
$wpdb->query("DROP TABLE IF EXISTS wspo_plugin_register");

unlink(WPMU_PLUGIN_DIR . '/wspo-mu-load.php');
?>