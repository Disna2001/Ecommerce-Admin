<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $siteName;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->siteName = SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[New Order Received] #' . $this->order->order_number . ' - ' . $this->order->customer_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-order-received',
            with: [
                'order' => $this->order,
                'siteName' => $this->siteName,
            ],
        );
    }
}
