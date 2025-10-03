<?php
if (!defined('ABSPATH')) exit;

add_shortcode('recently_viewed_products', 'acav_recently_viewed_shortcode');
function acav_recently_viewed_shortcode($atts) {
    //Get Viewed Product IDs
    $product_ids = acav_get_recently_viewed();
    if (empty($product_ids)) return '';
    $args = [
        'post_type' => 'product',
        'post__in' => $product_ids,
        'orderby' => 'post__in',
        'posts_per_page' => 5,
    ];
    $query = new WP_Query($args);
    ob_start();
    include ACAV_PATH . 'templates/recently-viewed-products.php';
    return ob_get_clean();
}

add_shortcode('frequently_viewed_together', 'acav_frequently_viewed_shortcode');
function acav_frequently_viewed_shortcode($atts) {
    global $wpdb;

    $atts = shortcode_atts(['product_id' => 0], $atts);
    $product_id = absint($atts['product_id']);
    if (!$product_id) return '';

    $table_name = $wpdb->prefix . 'acav_related_products';

    //Prepare Transient
    $transient_key = 'acav_related_products_'.$product_id;
    $related_ids = get_transient($transient_key);

    if ($related_ids === false) {
        //Get High Score Products
        $related_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT related_product_id 
         FROM $table_name 
         WHERE product_id = %d 
         ORDER BY score DESC 
         LIMIT 5",
            $product_id
        ));
        set_transient($transient_key, $related_ids, DAY_IN_SECONDS);
    }

    if (empty($related_ids)) return '';

    $args = [
        'post_type'      => 'product',
        'post__in'       => $related_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => 5,
    ];
    $query = new WP_Query($args);

    ob_start();
    include ACAV_PATH . 'templates/frequently-viewed-together.php';
    return ob_get_clean();
}
