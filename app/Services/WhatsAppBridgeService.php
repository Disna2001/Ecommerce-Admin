<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsAppBridgeService
 *
 * Thin HTTP client wrapping the 4 endpoints exposed by the whatsapp-bridge Node.js service.
 * Base URL and shared secret are read from app config (env WHATSAPP_BRIDGE_URL / WHATSAPP_BRIDGE_SECRET).
 */
class WhatsAppBridgeService
{
    protected string $baseUrl;
    protected string $secret;
    protected int    $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.whatsapp_bridge.url', 'http://127.0.0.1:3000'), '/');
        $this->secret  = config('services.whatsapp_bridge.secret', '');
        $this->timeout = (int) config('services.whatsapp_bridge.timeout', 8);
    }

    /**
     * GET /qr — returns array with keys: qr, image (base64 data URL), expires_at
     * Returns null when no QR is available (already connected, or bridge unreachable).
     */
    public function getQr(): ?array
    {
        try {
            $response = $this->client()->get('/qr');

            if ($response->status() === 503) {
                return null;
            }

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Bridge /qr failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * GET /status — returns array with keys: connected, phone_number, state
     */
    public function getStatus(): array
    {
        try {
            $response = $this->client()->get('/status');
            return $response->successful() ? $response->json() : [
                'connected'    => false,
                'phone_number' => null,
                'state'        => 'disconnected',
            ];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Bridge /status failed: ' . $e->getMessage());
            return [
                'connected'    => false,
                'phone_number' => null,
                'state'        => 'unreachable',
            ];
        }
    }

    /**
     * POST /send-message — send a text message to a phone number.
     * @param string $to E.164 or digits-only phone number.
     */
    public function sendMessage(string $to, string $message): bool
    {
        try {
            $response = $this->client()->post('/send-message', [
                'to'      => $to,
                'message' => $message,
            ]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Bridge /send-message failed: ' . $e->getMessage(), ['to' => $to]);
            return false;
        }
    }

    /**
     * POST /logout — unlink the WhatsApp session.
     */
    public function logout(): bool
    {
        try {
            $response = $this->client()->post('/logout');
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Bridge /logout failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Whether the bridge service is reachable and connected.
     */
    public function isConnected(): bool
    {
        return $this->getStatus()['connected'] ?? false;
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    protected function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->acceptJson()
            ->withHeaders(array_filter([
                'X-Bridge-Secret' => $this->secret ?: null,
            ]));
    }
}
