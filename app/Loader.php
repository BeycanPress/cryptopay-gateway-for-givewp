<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\GiveWP;

use BeycanPress\CryptoPay\Integrator\Hook;
use BeycanPress\CryptoPay\Integrator\Helpers;
use Give\Framework\PaymentGateways\PaymentGatewayRegister;

class Loader
{
    /**
     * Loader constructor.
     */
    public function __construct()
    {
        Helpers::registerIntegration('givewp');

        // add transaction page
        Helpers::createTransactionPage(
            esc_html__('GiveWP Transactions', 'givewp-cryptopay'),
            'givewp',
            10,
            [
                'orderId' => function ($tx) {
                    return Helpers::run('view', 'components/link', [
                        'url' => sprintf(admin_url('edit.php?post_type=give_forms&page=give-payment-history&view=view-payment-details&id=%d'), $tx->orderId), // @phpcs:ignore
                        'text' => sprintf(esc_html__('View donate #%d', 'gf-cryptopay'), $tx->orderId)
                    ]);
                }
            ]
        );

        add_action('init', [Helpers::class, 'listenSPP']);
        Hook::addFilter('payment_redirect_urls_givewp', [$this, 'paymentRedirectUrls']);
        add_action('givewp_register_payment_gateway', [Loader::class, 'registerPaymentGateway']);
        add_filter('give_payment_details_transaction_id-cryptopay', [$this, 'transactionId']);
        add_filter('give_payment_details_transaction_id-cryptopay-lite', [$this, 'transactionIdLite']);
    }

    /**
     * @param string $transactionId
     * @return string
     */
    public function transactionId(string $transactionId): string
    {
        return Helpers::run('view', 'components/link', [
            'url' => sprintf(admin_url('admin.php?page=cryptopay_givewp_transactions&s=%s'), $transactionId),
            'text' => sprintf(esc_html__('View transaction #%s', 'gf-cryptopay'), $transactionId)
        ]);
    }

    /**
     * @param string $transactionId
     * @return string
     */
    public function transactionIdLite(string $transactionId): string
    {
        return Helpers::run('view', 'components/link', [
            'url' => sprintf(admin_url('admin.php?page=cryptopay_lite_givewp_transactions&s=%s'), $transactionId),
            'text' => sprintf(esc_html__('View transaction #%s', 'gf-cryptopay'), $transactionId)
        ]);
    }

    /**
     * @param object $data
     * @return array<string,string>
     */
    public function paymentRedirectUrls(object $data): array
    {
        $token = $data->getParams()->get('token');
        $successUrl = $data->getParams()->get('returnUrl');
        $successUrl = str_replace([
            'cp_payment_token',
            'cp_transaction_id'
        ], [
            $token,
            $data->getHash(),
        ], $successUrl);

        return [
            'success' => $successUrl,
            'failed' => give_get_failed_transaction_uri()
        ];
    }

    /**
     * Register the payment gateway.
     * @param PaymentGatewayRegister $registry
     * @return void
     */
    public static function registerPaymentGateway(PaymentGatewayRegister $registry): void
    {
        $registry->registerGateway(Gateways\GatewayPro::class);
        $registry->registerGateway(Gateways\GatewayLite::class);
    }
}
