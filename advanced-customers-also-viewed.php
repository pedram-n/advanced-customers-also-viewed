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


add_action('wp', 'acav_track_product_view');
function acav_track_product_view()
{
    if (is_product()) {
        global $post;
        acav_set_recently_viewed($post->ID);
    }
}

//Crate Cron Job
add_action('acav_cron_job', 'acav_generate_frequently_viewed_data');
if (!wp_next_scheduled('acav_cron_job')) {
    $timestamp = strtotime('02:00:00');
    if ($timestamp <= time()) {
        $timestamp = strtotime('tomorrow 02:00:00');
    }
    wp_schedule_event($timestamp, 'daily', 'acav_cron_job');
}
