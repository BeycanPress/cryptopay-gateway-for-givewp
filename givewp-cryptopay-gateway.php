<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

// @phpcs:disable PSR1.Files.SideEffects
// @phpcs:disable PSR12.Files.FileHeader
// @phpcs:disable Generic.Files.InlineHTML
// @phpcs:disable Generic.Files.LineLength

/**
 * Plugin Name: GiveWP - CryptoPay Gateway
 * Version:     1.0.0
 * Plugin URI:  https://beycanpress.com/cryptopay/
 * Description: Adds Cryptocurrency payment gateway (CryptoPay) for GiveWP.
 * Author:      BeycanPress LLC
 * Author URI:  https://beycanpress.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: givewp-cryptopay
 * Tags: Cryptopay, Cryptocurrency, WooCommerce, WordPress, MetaMask, Trust, Binance, Wallet, Ethereum, Bitcoin, Binance smart chain, Payment, Plugin, Gateway, Moralis, Converter, API, coin market cap, CMC
 * Requires at least: 5.0
 * Tested up to: 6.4.3
 * Requires PHP: 8.1
*/

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

define('GIVEWP_CRYPTOPAY_FILE', __FILE__);
define('GIVEWP_CRYPTOPAY_VERSION', '1.0.0');
define('GIVEWP_CRYPTOPAY_KEY', basename(__DIR__));
define('GIVEWP_CRYPTOPAY_URL', plugin_dir_url(__FILE__));
define('GIVEWP_CRYPTOPAY_DIR', plugin_dir_path(__FILE__));
define('GIVEWP_CRYPTOPAY_SLUG', plugin_basename(__FILE__));

use BeycanPress\CryptoPay\Integrator\Helpers;

Helpers::registerModel(BeycanPress\CryptoPay\GiveWP\Models\TransactionsPro::class);
Helpers::registerLiteModel(BeycanPress\CryptoPay\GiveWP\Models\TransactionsLite::class);

load_plugin_textdomain('givewp-cryptopay', false, basename(__DIR__) . '/languages');

if (!defined('GIVE_VERSION')) {
    add_action('admin_notices', function (): void {
        ?>
            <div class="notice notice-error">
                <p><?php echo sprintf(esc_html__('GiveWP - CryptoPay Gateway: This plugin requires GiveWP to work. You can download GiveWP by %s.', 'givewp-cryptopay'), '<a href="https://wordpress.org/plugins/give/" target="_blank">' . esc_html__('clicking here', 'givewp-cryptopay') . '</a>'); ?></p>
            </div>
        <?php
    });
} elseif (Helpers::bothExists()) {
    new BeycanPress\CryptoPay\GiveWP\Loader();
} else {
    add_action('admin_notices', function (): void {
        ?>
            <div class="notice notice-error">
                <p><?php echo sprintf(esc_html__('GiveWP - CryptoPay Gateway: This plugin is an extra feature plugin so it cannot do anything on its own. It needs CryptoPay to work. You can buy CryptoPay by %s.', 'givewp-cryptopay'), '<a href="https://beycanpress.com/product/cryptopay-all-in-one-cryptocurrency-payments-for-wordpress/?utm_source=wp_org_addons&utm_medium=givewp" target="_blank">' . esc_html__('clicking here', 'givewp-cryptopay') . '</a>'); ?></p>
            </div>
        <?php
    });
}
