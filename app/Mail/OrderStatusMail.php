<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $stage;
    public ?string $customMessage;
    public array $meta;
    public string $siteName;

    public function __construct(Order $order, string $stage = 'status_update', ?string $customMessage = null)
    {
        $this->order = $order;
        $this->stage = $stage;
        $this->customMessage = $customMessage;
        $this->siteName = SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
        $this->meta = $this->resolveMeta($stage);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->meta['subject'] . ' - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
            with: [
                'order' => $this->order,
                'siteName' => $this->siteName,
                'meta' => $this->meta,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    protected function resolveMeta(string $stage): array
    {
        return match ($stage) {
            'created' => [
                'subject' => 'We received your order',
                'title' => 'Order placed successfully',
                'summary' => 'Your order is now in our system and our team will guide it through the next steps.',
                'accent' => '#4f46e5',
            ],
            'payment_submitted' => [
                'subject' => 'Payment proof received',
                'title' => 'Payment submitted for review',
                'summary' => 'We received your payment reference and proof. Our team will verify it and update you soon.',
                'accent' => '#2563eb',
            ],
            'payment_approved' => [
                'subject' => 'Payment approved',
                'title' => 'Your payment was approved',
                'summary' => 'Your payment has been verified successfully and your order is moving forward.',
                'accent' => '#059669',
            ],
            'payment_rejected' => [
                'subject' => 'Payment needs attention',
                'title' => 'Your payment proof needs correction',
                'summary' => 'We could not verify the submitted payment proof. Please review the note and submit a corrected proof if needed.',
                'accent' => '#dc2626',
            ],
            'confirmed' => [
                'subject' => 'Order confirmed',
                'title' => 'Your order is confirmed',
                'summary' => 'Our team has confirmed your order and it is now moving into preparation.',
                'accent' => '#3b82f6',
            ],
            'processing' => [
                'subject' => 'Order in progress',
                'title' => 'We are preparing your order',
                'summary' => 'Your order is now being prepared for dispatch.',
                'accent' => '#7c3aed',
            ],
            'shipped' => [
                'subject' => 'Order shipped',
                'title' => 'Your order is on the way',
                'summary' => 'Your order has been shipped. Use the tracking details below to follow delivery progress.',
                'accent' => '#0891b2',
            ],
            'delivered' => [
                'subject' => 'Order delivered',
                'title' => 'Your order was delivered',
                'summary' => 'Your order has been marked as delivered. We hope everything arrived exactly as expected.',
                'accent' => '#16a34a',
            ],
            'completed' => [
                'subject' => 'Order completed',
                'title' => 'Your order is complete',
                'summary' => 'Your order has been completed successfully. Thank you for shopping with us.',
                'accent' => '#15803d',
            ],
            'cancelled' => [
                'subject' => 'Order cancelled',
                'title' => 'Your order was cancelled',
                'summary' => 'This order has been cancelled. If you need help, please contact our team.',
                'accent' => '#dc2626',
            ],
            'return_approved' => [
                'subject' => 'Return approved',
                'title' => 'Your return request was approved',
                'summary' => 'Our team approved your return request. Please follow the next instructions shared below.',
                'accent' => '#d97706',
            ],
            'refunded' => [
                'subject' => 'Refund completed',
                'title' => 'Your refund has been processed',
                'summary' => 'A refund has been processed for your order. Please allow some time for it to appear in your account.',
                'accent' => '#7c3aed',
            ],
            default => [
                'subject' => 'Order update',
                'title' => 'There is a new update on your order',
                'summary' => 'We have an important update for your order.',
                'accent' => '#4f46e5',
            ],
        };
    }
}
