<?php

namespace Webkul\Stripe\Payment;

use Webkul\Payment\Payment\Payment;
use Illuminate\Support\Facades\Storage;
use Webkul\Checkout\Facades\Cart;

class Stripe extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'stripe';

    public function getRedirectUrl(): string
    {
        return route('stripe.process');
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return core()->getConfigData('sales.payment_methods.stripe.active');
    }

    /**
     * Get payment method image.
     *
     * @return array
     */
    public function getImage(): string
    {
        $url = core()->getConfigData('sales.payment_methods.stripe.logo_image') ;//$this->getConfigData('image');

        return $url ? Storage::url($url) : '';
    }
}
