<?php
if (!defined('ABSPATH')) exit;

//Create DB Tables
function acav_create_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acav_related_products';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) UNSIGNED NOT NULL,
        related_product_id BIGINT(20) UNSIGNED NOT NULL,
        score BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY  (id),
        KEY product_id (product_id),
        KEY related_product_id (related_product_id)
    ) $charset_collate;";

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

//Delete DB Tables
function acav_delete_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acav_related_products';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

//Create Cron Job
function acav_schedule_cron_job() {
    if (!wp_next_scheduled('acav_cron_job')) {
        $wp_timezone = wp_timezone();
        $datetime = new DateTime('today 02:00:00', $wp_timezone);

        if ($datetime->getTimestamp() <= time()) {
            $datetime = new DateTime('tomorrow 02:00:00', $wp_timezone);
        }

        $datetime->setTimezone(new DateTimeZone('UTC'));
        $timestamp = $datetime->getTimestamp();

        wp_schedule_event($timestamp, 'daily', 'acav_cron_job');
    }
}

//Delete Cron Job
function acav_clear_cron_job() {
    $timestamp = wp_next_scheduled('acav_cron_job');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'acav_cron_job');
    }
}

//Get Viewed Data
function acav_get_recently_viewed() {
    if (is_user_logged_in()) {
        return get_user_meta(get_current_user_id(), 'acav_recently_viewed', true) ?: [];
    } else {
        return json_decode(wp_unslash($_COOKIE['acav_recently_viewed'] ?? '[]'), true) ?: [];
    }
}

//Set Product ID on View
function acav_set_recently_viewed($product_id) {
    $product_id = absint($product_id);
    if (!$product_id) {
        return;
    }

    $recent = acav_get_recently_viewed();

    //Validate Data
    $recent = array_filter($recent, 'absint');
    $recent = array_map('absint', $recent);

    //Remove ID if Exist
    if (($key = array_search($product_id, $recent)) !== false) {
        unset($recent[$key]);
    }

    //Add Product ID
    array_unshift($recent, $product_id);
    $recent = array_slice($recent, 0, 20);

    //Save Data
    if (is_user_logged_in()) {
        update_user_meta(get_current_user_id(), 'acav_recently_viewed', $recent);
    } else {
        setcookie('acav_recently_viewed', wp_json_encode($recent), time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
    }
}

//Get Related Products
function acav_get_related_products($product_id, $limit = 5) {
    global $wpdb;
    $product_id = absint($product_id);
    if (!$product_id) return [];

    $transient_key = "acav_related_products_{$product_id}";
    $related_ids = get_transient($transient_key);

    if ($related_ids === false) {
        $table_name = $wpdb->prefix . 'acav_related_products';
        $related_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT related_product_id 
             FROM $table_name 
             WHERE product_id = %d 
             ORDER BY score DESC 
             LIMIT %d",
            $product_id,
            $limit
        ));

        set_transient($transient_key, $related_ids, DAY_IN_SECONDS);
    }

    return $related_ids;
}

//Option Page Handler
function acav_option_page_handler()
{
    require_once ACAV_PATH . 'templates/admin-main-option.php';
}

//Admin Form Handler
add_action( 'admin_init', function() {
    if ( isset( $_POST['acav_regenerate_data'] ) ) {
        if ( ! isset( $_POST['acav_regenerate_nonce'] ) || ! wp_verify_nonce( $_POST['acav_regenerate_nonce'], 'acav_regenerate_data_action' ) ) {
            wp_die( __( 'Security check failed.', 'advanced-customers-also-viewed' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have permission to perform this action.', 'advanced-customers-also-viewed' ) );
        }

        if ( function_exists( 'acav_generate_frequently_viewed_data' ) ) {
            acav_generate_frequently_viewed_data();
            $status = 'success';
        } else {
            $status = 'error';
        }

        wp_redirect( add_query_arg( 'acav_status', $status, menu_page_url( 'acav-options', false ) ) );
        exit;
    }
});

