<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $type; // 'upcoming' or 'overdue'

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, string $type)
    {
        $this->invoice = $invoice;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->type === 'overdue' 
            ? 'Action Required: Rent Payment Overdue' 
            : 'Reminder: Upcoming Rent Payment';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rent_reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
