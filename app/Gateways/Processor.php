<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\GiveWP\Gateways;

// CP
use BeycanPress\CryptoPay\Integrator\Type;
use BeycanPress\CryptoPay\Integrator\Helpers;
use BeycanPress\CryptoPay\Integrator\Session;
// GiveWP
use Give\Helpers\Language;
use Give\Helpers\Form\Template;
use Give\Donations\Models\Donation;
use Give\Donations\Models\DonationNote;
use Give\Donations\ValueObjects\DonationStatus;
use Give\Framework\Exceptions\Primitives\Exception;
use Give\Framework\Exceptions\Primitives\RuntimeException;
use Give\Framework\Http\Response\Types\RedirectResponse;
use Give\Framework\PaymentGateways\Commands\RedirectOffsite;

class Processor
{
    /**
     * @var bool
     */
    private static bool $scriptLoaded = false;

    /**
     * This is activate visual form builder gateway enqueueScript()
     * @param string $id
     * @param int $formId
     * @return void
     */
    public static function enqueueScript(string $id, int $formId): void
    {
        if (self::$scriptLoaded) {
            return;
        }

        self::$scriptLoaded = true;

        wp_enqueue_script(
            $id,
            GIVEWP_CRYPTOPAY_URL . 'assets/js/main.js',
            ['wp-element', 'wp-i18n'],
            GIVEWP_CRYPTOPAY_VERSION,
            true
        );
        Language::setScriptTranslations($id);
    }

    /**
     * @param int $formId
     * @param array<mixed> $args
     * @return string
     */
    public static function getLegacyFormFieldMarkup(int $formId, array $args): string
    {
        return "<span> " . esc_html__(
            'You can pay with supported networks and cryptocurrencies.',
            'givewp-cryptopay'
        ) . " </span>";
    }

    /**
     * @param Type $type
     * @param Donation $donation
     * @param string $returnUrl
     * @return RedirectOffsite
     */
    // @phpcs:ignore
    public static function createPayment(Type $type, Donation $donation, string $returnUrl): RedirectOffsite
    {
        $paymentPageLink = Helpers::createSPP([
            'addon' => 'givewp',
            'addonName' => 'GiveWP',
            'order' => [
                'id' => $donation->id,
                'amount' => $donation->amount->formatToDecimal(),
                'currency' => give_get_currency($donation->id),
            ],
            'params' => [
                'returnUrl' => $returnUrl,
            ],
            'type' => $type,
        ]);

        update_post_meta($donation->id, 'givewp_cp_payment_url', $paymentPageLink);

        return new RedirectOffsite($paymentPageLink);
    }

    /**
     * @param int $formId
     * @return bool
     */
    private static function isClassicForm(int $formId): bool
    {
        return 'classic' === Template::getActiveID($formId);
    }

    /**
     * An example of using a secureRouteMethod for extending the Gateway API to handle a redirect.
     *
     * @param string $name
     * @param array<mixed> $queryParams
     * @return RedirectResponse
     * @throws \Exception
     */
    public static function handleCreatePaymentRedirect(string $name, array $queryParams): RedirectResponse
    {
        $donationId = absint($queryParams['givewp-donation-id']);
        $token = sanitize_text_field($queryParams['givewp-payment-token']);
        $successUrl = sanitize_text_field($queryParams['givewp-success-url']);
        $transactionId = sanitize_text_field($queryParams['givewp-transaction-id']);

        $donation = Donation::find($donationId);

        $donation->status = DonationStatus::COMPLETE();
        $donation->gatewayTransactionId = $transactionId;
        $donation->save();

        DonationNote::create([
            'donationId' => $donation->id,
            'content' => sprintf(esc_html__('Donation completed with %s', 'givewp-cryptopay'), $name),
        ]);

        Session::remove($token); // Remove the token from the session.

        return new RedirectResponse($successUrl);
    }

    /**
     * @param Donation $donation
     * @return void
     */
    public static function refundDonation(Donation $donation): void
    {
        throw new RuntimeException(
            'Method has not been implemented yet. Please make refund manually.'
        );
    }
}
