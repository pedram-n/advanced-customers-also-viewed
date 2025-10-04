<?php
/*
Plugin Name: Advanced Customers Also Viewed
Plugin URI: https://snapppay.ir/
Description: Tracks product views and displays "Recently Viewed" and "Frequently Viewed Together" products.
Author: Pedram Nasertorabi
Version: 1.0.0
Author URI: https://n-pedram.ir/
Text Domain: advanced-customers-also-viewed
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if (!defined('ABSPATH')) exit;

define('ACAV_PATH', plugin_dir_path(__FILE__));
define('ACAV_URL', plugin_dir_url(__FILE__));

require_once ACAV_PATH . 'includes/acav-functions.php';
require_once ACAV_PATH . 'includes/acav-shortcodes.php';
require_once ACAV_PATH . 'includes/acav-cron.php';

add_action('wp_enqueue_scripts', 'acav_enqueue_assets');
function acav_enqueue_assets() {
        wp_enqueue_style('acav-styles', ACAV_URL . 'assets/css/style.css', array(), '1.0.0');
}

add_action('wp', 'acav_track_product_view');
function acav_track_product_view()
{
    if (is_product()) {
        global $post;
        acav_set_recently_viewed($post->ID);
    }
}

register_activation_hook(__FILE__, 'acav_activate_plugin');
function acav_activate_plugin() {
    acav_create_tables();
    acav_schedule_cron_job();
}

register_deactivation_hook(__FILE__, 'acav_deactivate_plugin');
function acav_deactivate_plugin() {
    acav_clear_cron_job();
}

register_uninstall_hook(__FILE__, 'acav_uninstall_plugin');
function acav_uninstall_plugin() {
    acav_clear_cron_job();
    acav_delete_tables();
}

add_action('acav_cron_job', 'acav_generate_frequently_viewed_data');