<?php
function acav_generate_frequently_viewed_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acav_related_products';

    //Remove Old Table
    $wpdb->query("TRUNCATE TABLE $table_name");

    $all_users = get_users();
    $relations = [];

    foreach ($all_users as $user) {
        $views = get_user_meta($user->ID, 'acav_recently_viewed', true) ?: [];
        $views = array_map('absint', (array) $views);
        $views = array_filter($views);
        foreach ($views as $product_id) {
            foreach ($views as $related_pid) {
                if ($product_id !== $related_pid) {
                    $relations[$product_id][$related_pid] = ($relations[$product_id][$related_pid] ?? 0) + 1;
                }
            }
        }
    }

    foreach ($relations as $product_id => $related) {
        arsort($related);
        $related_products = array_slice($related, 0, 5, true);
        foreach ($related_products as $related_pid => $score) {
            $wpdb->insert(
                $table_name,
                array(
                    'product_id'        => $product_id,
                    'related_product_id'=> $related_pid,
                    'score'             => $score
                ),
                ['%d', '%d', '%d']
            );
        }
        delete_transient('acav_related_products_'.$product_id);
    }
}
acav_generate_frequently_viewed_data();