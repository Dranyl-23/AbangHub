<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invoice;

class RentDueNotification extends Notification
{
    use Queueable;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Can add 'mail' later if needed
    }

    public function toDatabase(object $notifiable): array
    {
        $formattedAmount = number_format($this->invoice->amount, 0);
        return [
            'type' => 'invoice',
            'invoice_id' => $this->invoice->id,
            'message' => "Hello, ang imong abang nga ₱{$formattedAmount} kay due na. Palihog bayad before the due date.",
        ];
    }
}
