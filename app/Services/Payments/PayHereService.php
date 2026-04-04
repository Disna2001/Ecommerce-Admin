<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\SiteSetting;

class PayHereService
{
    public function isEnabled(): bool
    {
        return (bool) SiteSetting::get('enable_payhere_gateway', false);
    }

    public function isConfigured(): bool
    {
        return filled($this->merchantId()) && filled($this->merchantSecret());
    }

    public function checkoutUrl(): string
    {
        return $this->sandboxEnabled()
            ? 'https://sandbox.payhere.lk/pay/checkout'
            : 'https://www.payhere.lk/pay/checkout';
    }

    public function buildCheckoutPayload(Order $order): array
    {
        $amount = number_format((float) $order->total, 2, '.', '');
        $currency = 'LKR';

        return [
            'merchant_id' => $this->merchantId(),
            'return_url' => $this->publicRoute('checkout.payhere.return', ['order' => $order->order_number]),
            'cancel_url' => $this->publicRoute('checkout.payhere.cancel', ['order' => $order->order_number]),
            'notify_url' => $this->publicRoute('checkout.payhere.notify'),
            'order_id' => $order->order_number,
            'items' => 'Order ' . $order->order_number,
            'currency' => $currency,
            'amount' => $amount,
            'first_name' => $this->firstName($order->customer_name),
            'last_name' => $this->lastName($order->customer_name),
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'address' => $order->shipping_address,
            'city' => $order->shipping_city,
            'country' => $order->shipping_country ?: 'Sri Lanka',
            'delivery_address' => $order->shipping_address,
            'delivery_city' => $order->shipping_city,
            'delivery_country' => $order->shipping_country ?: 'Sri Lanka',
            'custom_1' => (string) $order->id,
            'custom_2' => (string) $order->user_id,
            'hash' => $this->generateRequestHash(
                $this->merchantId(),
                $order->order_number,
                $amount,
                $currency
            ),
        ];
    }

    public function verifyNotification(array $payload): bool
    {
        $merchantId = (string) ($payload['merchant_id'] ?? '');
        $orderId = (string) ($payload['order_id'] ?? '');
        $amount = (string) ($payload['payhere_amount'] ?? '');
        $currency = (string) ($payload['payhere_currency'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $signature = strtoupper((string) ($payload['md5sig'] ?? ''));

        if (!$this->isConfigured() || $merchantId !== $this->merchantId() || $signature === '') {
            return false;
        }

        $localSignature = strtoupper(md5(
            $merchantId .
            $orderId .
            $amount .
            $currency .
            $statusCode .
            strtoupper(md5($this->merchantSecret()))
        ));

        return hash_equals($localSignature, $signature);
    }

    public function sandboxEnabled(): bool
    {
        return (bool) SiteSetting::get('payhere_sandbox', true);
    }

    public function notifyUrlLooksPublic(): bool
    {
        $host = parse_url($this->publicRoute('checkout.payhere.notify'), PHP_URL_HOST);

        return filled($host) && !in_array($host, ['localhost', '127.0.0.1', 'ecommerce-admin.test'], true);
    }

    protected function merchantId(): string
    {
        return (string) SiteSetting::get('payhere_merchant_id', '');
    }

    protected function merchantSecret(): string
    {
        return (string) SiteSetting::get('payhere_merchant_secret', '');
    }

    protected function generateRequestHash(string $merchantId, string $orderId, string $amount, string $currency): string
    {
        return strtoupper(md5(
            $merchantId .
            $orderId .
            $amount .
            $currency .
            strtoupper(md5($this->merchantSecret()))
        ));
    }

    protected function publicRoute(string $name, array $parameters = []): string
    {
        $path = route($name, $parameters, false);
        $baseUrl = rtrim((string) (SiteSetting::get('app_public_url') ?: config('app.url') ?: url('/')), '/');

        return $baseUrl . $path;
    }

    protected function firstName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        return $parts[0] ?? 'Customer';
    }

    protected function lastName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];

        return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '-';
    }
}
