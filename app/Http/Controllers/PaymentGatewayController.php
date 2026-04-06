<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Orders\OrderWorkflowService;
use App\Services\Payments\PayHereService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentGatewayController extends Controller
{
    public function payhereRedirect(string $order, PayHereService $payHereService)
    {
        $orderModel = Order::query()
            ->where('order_number', $order)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        abort_unless($payHereService->isEnabled(), 404);

        if (!$payHereService->isConfigured()) {
            return redirect()
                ->route('checkout.success', ['order' => $orderModel->order_number])
                ->with('gateway_error', 'PayHere is enabled but not fully configured yet.');
        }

        return view('frontend.checkout.payhere-redirect', [
            'order' => $orderModel,
            'actionUrl' => $payHereService->checkoutUrl(),
            'payload' => $payHereService->buildCheckoutPayload($orderModel),
            'notifyUrlLooksPublic' => $payHereService->notifyUrlLooksPublic(),
            'sandboxEnabled' => $payHereService->sandboxEnabled(),
        ]);
    }

    public function payhereReturn(string $order)
    {
        session()->forget(['cart', 'cart_discount', 'coupon_code']);

        return redirect()
            ->route('checkout.success', ['order' => $order])
            ->with('gateway_notice', 'Payment return received. We are waiting for final confirmation from PayHere.');
    }

    public function payhereCancel(string $order, OrderWorkflowService $orderWorkflowService)
    {
        $orderModel = Order::query()->where('order_number', $order)->firstOrFail();

        if ($orderModel->payment_status !== 'paid') {
            $orderWorkflowService->syncGatewayPayment($orderModel, 'failed', [
                'note' => 'Customer cancelled the PayHere payment before completion.',
            ]);
        }

        return redirect()
            ->route('checkout.success', ['order' => $orderModel->order_number])
            ->with('gateway_error', 'The PayHere payment was cancelled. You can place the order again if needed.');
    }

    public function payhereNotify(
        Request $request,
        PayHereService $payHereService,
        OrderWorkflowService $orderWorkflowService
    ): Response {
        $payload = $request->all();

        if (!$payHereService->verifyNotification($payload)) {
            return response('invalid signature', 400);
        }

        $order = Order::query()->where('order_number', $payload['order_id'] ?? null)->first();

        if (!$order) {
            return response('order not found', 404);
        }

        $statusCode = (string) ($payload['status_code'] ?? '');
        $gatewayData = [
            'gateway' => 'payhere',
            'payment_reference' => $payload['payment_id'] ?? $order->payment_reference,
            'note' => trim('PayHere callback: ' . ($payload['status_message'] ?? 'Payment update received.')),
            'method' => $payload['method'] ?? null,
            'status_code' => $statusCode,
            'payload' => $payload,
        ];

        if ($statusCode === '2') {
            $orderWorkflowService->syncGatewayPayment($order, 'paid', $gatewayData);

            return response('ok', 200);
        }

        if (in_array($statusCode, ['-1', '-2', '-3'], true) && $order->payment_status !== 'paid') {
            $orderWorkflowService->syncGatewayPayment($order, 'failed', $gatewayData);
        }

        return response('ok', 200);
    }
}
