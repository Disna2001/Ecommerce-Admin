<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $siteName = SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
        $siteEmail = SiteSetting::get('support_email', config('mail.from.address', 'company@example.com'));
        $sitePhone = SiteSetting::get('support_phone', '+94 11 234 5678');
        $siteAddress = SiteSetting::get('company_address', 'Sri Lanka');
        $this->company = [
            'name' => $siteName,
            'email' => $siteEmail,
            'phone' => $sitePhone,
            'address' => $siteAddress,
            'tax_id' => SiteSetting::get('company_tax_id', 'N/A'),
            'currency' => 'LKR',
            'currency_symbol' => 'Rs',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice #' . $this->invoice->invoice_number . ' - Payment Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'company' => $this->company,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $pdf = PDF::loadView('exports.invoice-pdf', [
            'invoice' => $this->invoice,
            'company' => $this->company
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'invoice-' . $this->invoice->invoice_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
