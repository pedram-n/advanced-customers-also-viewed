<?php
function acav_generate_frequently_viewed_data() {
    $all_users = get_users();
    $relations = [];
    foreach ($all_users as $user) {
        $views = get_user_meta($user->ID, 'acav_recently_viewed', true) ?: [];
        foreach ($views as $product_id) {
            foreach ($views as $related_pid) {
                if ($product_id !== $related_pid) {
                    $relations[$product_id][$related_pid] = ($relations[$product_id][$related_pid] ?? 0) + 1;
                }
            }
        }
    }
    foreach ($relations as $pid => $related) {
        arsort($related);
        $related_products = array_slice(array_keys($related), 0, 5);
        update_option("acav_related_products_{$pid}", $related_products);
    }
}