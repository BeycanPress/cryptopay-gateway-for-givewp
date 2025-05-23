<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

// @phpcs:disable PSR1.Files.SideEffects
// @phpcs:disable PSR12.Files.FileHeader
// @phpcs:disable Generic.Files.InlineHTML
// @phpcs:disable Generic.Files.LineLength

/**
 * Plugin Name: CryptoPay Gateway for GiveWP
 * Version:     1.0.2
 * Plugin URI:  https://beycanpress.com/cryptopay/
 * Description: Adds Cryptocurrency payment gateway (CryptoPay) for GiveWP.
 * Author:      BeycanPress LLC
 * Author URI:  https://beycanpress.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: cryptopay-gateway-for-givewp
 * Tags: Bitcoin, Ethereum, Crypto, Payment, GiveWP
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 8.1
*/

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

define('GIVEWP_CRYPTOPAY_FILE', __FILE__);
define('GIVEWP_CRYPTOPAY_VERSION', '1.0.2');
define('GIVEWP_CRYPTOPAY_KEY', basename(__DIR__));
define('GIVEWP_CRYPTOPAY_URL', plugin_dir_url(__FILE__));
define('GIVEWP_CRYPTOPAY_DIR', plugin_dir_path(__FILE__));
define('GIVEWP_CRYPTOPAY_SLUG', plugin_basename(__FILE__));

use BeycanPress\CryptoPay\GiveWP\Loader;
use BeycanPress\CryptoPay\Integrator\Helpers;

/**
 * Register models for GiveWP
 * @return void
 */
function fiveCryptoPayRegisterModels(): void
{
    Helpers::registerModel(BeycanPress\CryptoPay\GiveWP\Models\TransactionsPro::class);
    Helpers::registerLiteModel(BeycanPress\CryptoPay\GiveWP\Models\TransactionsLite::class);
}

fiveCryptoPayRegisterModels();

add_action('init', function (): void {
    load_plugin_textdomain('cryptopay-gateway-for-givewp', false, basename(__DIR__) . '/languages');
});

add_action('givewp_register_payment_gateway', [Loader::class, 'registerPaymentGateway']);

add_action('plugins_loaded', function (): void {
    fiveCryptoPayRegisterModels();

    if (!defined('GIVE_VERSION')) {
        Helpers::requirePluginMessage('GiveWP', admin_url('plugin-install.php?s=givewp&tab=search&type=term'));
    } elseif (Helpers::bothExists()) {
        new BeycanPress\CryptoPay\GiveWP\Loader();
    } else {
        Helpers::requireCryptoPayMessage('GiveWP');
    }
});
