<div class="wrap">
    <h1><?php _e('Also Viewed Options', 'advanced-customers-also-viewed'); ?></h1>
    <form method="post">
        <?php wp_nonce_field('acav_regenerate_data_action', 'acav_regenerate_nonce'); ?>
        <p><?php _e('regenerate frequently viewed together data.', 'advanced-customers-also-viewed') ?></p>
        <input type="submit" name="acav_regenerate_data" class="button button-primary"
               value="<?php esc_attr_e('Regenerate', 'advanced-customers-also-viewed'); ?>">
    </form>
    <?php if (isset($_GET['acav_status'])) {
        if ($_GET['acav_status'] === 'success') { ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Data regenerated successfully.', 'advanced-customers-also-viewed'); ?></p>
            </div>
        <?php } elseif ($_GET['acav_status'] === 'error') { ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e('Data regenerated failed.', 'advanced-customers-also-viewed'); ?></p>
            </div>
        <?php }
    } ?>
</div>




