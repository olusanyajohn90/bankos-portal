<?php
namespace App\Mail;

use App\Models\Account;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfficialStatementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public Account  $account,
        public string   $reference,
        public float    $fee,
        public string   $pdfBytes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Official Account Statement — ' . $this->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.official_statement',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, 'Statement-' . $this->reference . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
