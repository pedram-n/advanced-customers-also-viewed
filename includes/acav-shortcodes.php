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