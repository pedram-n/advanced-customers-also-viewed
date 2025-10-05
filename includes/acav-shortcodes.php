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
    $atts = shortcode_atts(['product_id' => 0], $atts);
    $product_id = absint($atts['product_id']);

    if (!$product_id && function_exists('is_product') && is_product()) {
        $product_id = get_queried_object_id();
    }

    if (!$product_id) return '';

    $related_ids = acav_get_related_products($product_id);
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
