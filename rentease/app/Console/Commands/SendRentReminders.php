<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendRentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-rent-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated rent reminder emails to tenants (upcoming and overdue).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay();

        // 1. Upcoming Reminders (Due in exactly 3 days)
        $upcomingInvoices = \App\Models\Invoice::with(['lease.tenant', 'lease.property'])
            ->where('status', 'pending')
            ->whereDate('due_date', $today->copy()->addDays(3))
            ->get();

        foreach ($upcomingInvoices as $invoice) {
            if ($invoice->lease && $invoice->lease->tenant) {
                \Illuminate\Support\Facades\Mail::to($invoice->lease->tenant->email)
                    ->send(new \App\Mail\RentReminderEmail($invoice, 'upcoming'));
                $this->info("Sent upcoming reminder to: {$invoice->lease->tenant->email}");
            }
        }

        // 2. Overdue Reminders (Overdue by exactly 1 day - to avoid spamming every day)
        $overdueInvoices = \App\Models\Invoice::with(['lease.tenant', 'lease.property'])
            ->where('status', 'pending')
            ->whereDate('due_date', $today->copy()->subDays(1))
            ->get();

        foreach ($overdueInvoices as $invoice) {
            if ($invoice->lease && $invoice->lease->tenant) {
                \Illuminate\Support\Facades\Mail::to($invoice->lease->tenant->email)
                    ->send(new \App\Mail\RentReminderEmail($invoice, 'overdue'));
                $this->info("Sent overdue reminder to: {$invoice->lease->tenant->email}");
            }
        }

        $this->info("Rent reminders processed successfully.");
    }
}
