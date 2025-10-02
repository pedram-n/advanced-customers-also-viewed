# Advanced Customers Also Viewed

A WordPress WooCommerce plugin to track product views and display "Recently Viewed" and "Frequently Viewed Together" products.

## Features
- Tracks product views for both logged-in and guest users.
- Displays "Recently Viewed" products via shortcode.
- Displays "Frequently Viewed Together" products via shortcode.
- WP-Cron job to calculate relationships for optimal performance.

## Installation
1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Configure plugin settings (if applicable).

## Usage
Once activated, the plugin automatically starts tracking product views.

### Shortcodes:
- `[recently_viewed_products]`  
  Displays the last five products viewed by the current user.

- `[frequently_viewed_together product_id="123"]`  
  Displays products frequently viewed together with the given product ID.

## Changelog
### 1.0.0
- Initial release of the plugin.

## License
This plugin is licensed under the GPLv2 or later.