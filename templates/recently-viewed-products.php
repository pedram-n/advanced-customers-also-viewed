<?php
if (!defined('ABSPATH')) exit;
if ($query->have_posts()) { ?>
    <div class="acav-recently-viewed">
        <h3><?php esc_html_e('Recently Viewed Products', 'advanced-customers-also-viewed'); ?></h3>
        <div class="acav-product-wrapper">
            <?php while ($query->have_posts()) {
                $query->the_post();
                global $product; ?>
                <div class="acav-product-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } ?>
                        <span class="acav-product-title"><?php echo esc_html( get_the_title() ); ?></span>
                        <span class="acav-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                    </a>
                </div>
                <?php
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
<?php } ?>
