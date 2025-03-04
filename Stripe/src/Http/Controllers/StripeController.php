<?php

namespace Webkul\Stripe\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Sales\Repositories\InvoiceRepository;
use Stripe\Stripe;

class StripeController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Constructor.
     *
     * @param OrderRepository $orderRepository
     * @param InvoiceRepository $invoiceRepository
     */
    public function __construct(OrderRepository $orderRepository, InvoiceRepository $invoiceRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Redirige al usuario a la página de pago de Stripe.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(Request $request)
    {
        $cart = Cart::getCart();

        if (!$cart) {
            return redirect()->route('shop.checkout.cart.index')->with('error', trans('stripe::app.cart_empty'));
        }

        Stripe::setApiKey(core()->getConfigData('sales.payment_methods.stripe.stripe_api_key'));

        // Calcular el monto total en centavos
        $total_amount = (int) round($cart->grand_total * 100);

        // Generar la descripción del producto basado en los ítems del carrito
        $product_name = trans('stripe::app.order_description', [
            'app_name' => config('app.name'),
            'order_id' => $cart->id,
        ]);

        // Crear la sesión de pago en Stripe
        try {

            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $cart->global_currency_code,
                        'product_data' => [
                            'name' => $product_name,
                        ],
                        'unit_amount' => (int)$total_amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
            ]);

            return redirect()->away($checkout_session->url);

        } catch (\Exception $e) {
            Log::error('Error al crear la sesión de pago de Stripe: ' . $e->getMessage());
            return redirect()->route('shop.checkout.cart.index')->with('error', trans('stripe::app.error_payment_initiation'));
        }
    }

    /**
     * Maneja la respuesta exitosa del pago.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        Stripe::setApiKey(core()->getConfigData('sales.payment_methods.stripe.stripe_api_key'));

        try {

            $session = \Stripe\Checkout\Session::retrieve($request->session_id);

            if ($session->payment_status === 'paid') {
                // Verificar que el carrito corresponde al pago
                $cart = Cart::getCart();

                if (!$cart) {
                    return redirect()->route('shop.checkout.cart.index')->with('error', trans('stripe::app.cart_not_found'));
                }

                // Crear el pedido
                $data = (new OrderResource($cart))->jsonSerialize();
                $order = $this->orderRepository->create($data);

                Cart::deActivateCart();

                //Es para ser usada en la siguiente peticion (redireccion) si se necesitara.
                //pero ahora no tiene ninguna utilidad.
                session()->flash('order_id', $order->id);

                return redirect()->route('shop.checkout.onepage.success');

            } else {
                return redirect()->route('shop.checkout.cart.index')->with('error', trans('stripe::app.payment_not_completed'));
            }

        } catch (\Exception $e) {
            Log::error('Error al verificar el pago de Stripe: ' . $e->getMessage());
            return redirect()->route('shop.checkout.cart.index')->with('error', trans('stripe::app.error_payment_verification'));
        }
    }

    /**
     * Maneja la cancelación del pago.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel()
    {
        return redirect()->route('shop.checkout.cart.index')->with('error', trans('stripe::app.payment_cancelled'));
    }

    /**
     * Prepara los datos de la factura del pedido.
     *
     * @param $order
     * @return array
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice' => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
