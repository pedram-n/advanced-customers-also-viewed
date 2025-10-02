<?php
if (!defined('ABSPATH')) exit;

function acav_get_user_id() {
    return is_user_logged_in() ? get_current_user_id() : null;
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


