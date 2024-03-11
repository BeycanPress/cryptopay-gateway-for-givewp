<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\GiveWP\Gateways;

use Give\Donations\Models\Donation;
use BeycanPress\CryptoPay\Integrator\Type;
use Give\Framework\PaymentGateways\PaymentGateway;
use Give\Framework\Exceptions\Primitives\Exception;
use Give\Framework\Http\Response\Types\RedirectResponse;
use Give\Framework\PaymentGateways\Commands\RedirectOffsite;

class GatewayLite extends PaymentGateway
{
    /**
     * @var array<string>
     */
    // @phpcs:ignore
    public $secureRouteMethods = [
        'handleCreatePaymentRedirect',
    ];

    /**
     * @return string
     */
    public static function id(): string
    {
        return 'cryptopay-lite';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'CryptoPay Lite';
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return self::id();
    }

    /**
     * @return string
     */
    public function getPaymentMethodLabel(): string
    {
        return $this->getName();
    }

    /**
     * This is activate visual form builder gateway enqueueScript()
     * @param int $formId
     * @return void
     */
    public function enqueueScript(int $formId): void
    {
        Processor::enqueueScript(self::id(), $formId);
    }

    /**
     * @param int $formId
     * @param array<mixed> $args
     * @return string
     */
    public function getLegacyFormFieldMarkup(int $formId, array $args): string
    {
        return Processor::getLegacyFormFieldMarkup($formId, $args);
    }

    /**
     * @param Donation $donation
     * @param array<mixed> $gatewayData
     * @return RedirectOffsite
     */
    // @phpcs:ignore
    public function createPayment(Donation $donation, $gatewayData): RedirectOffsite
    {
        return Processor::createPayment(Type::LITE, $donation, $this->generateSecureGatewayRouteUrl(
            'handleCreatePaymentRedirect',
            $donation->id,
            [
                'givewp-donation-id' => $donation->id,
                'givewp-payment-token' => 'cp_payment_token',
                'givewp-transaction-id' => 'cp_transaction_id',
                'givewp-success-url' => urlencode(give_get_success_page_uri()),
            ]
        ));
    }

    /**
     * An example of using a secureRouteMethod for extending the Gateway API to handle a redirect.
     *
     * @param array<mixed> $queryParams
     * @return RedirectResponse
     * @throws \Exception
     */
    protected function handleCreatePaymentRedirect(array $queryParams): RedirectResponse
    {
        return Processor::handleCreatePaymentRedirect($this->getName(), $queryParams);
    }

    /**
     * @param Donation $donation
     * @return void
     */
    public function refundDonation(Donation $donation): void
    {
        Processor::refundDonation($donation);
    }
}
