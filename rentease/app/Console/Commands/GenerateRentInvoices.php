<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lease;
use App\Models\Invoice;
use App\Notifications\RentDueNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateRentInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly rent invoices and send notifications to tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting invoice generation...');
        $today = Carbon::today();
        
        // Find all active leases
        $activeLeases = Lease::where('status', 'active')->get();
        $generatedCount = 0;

        foreach ($activeLeases as $lease) {
            // Check if today is the day rent is due based on the start date
            // e.g. If start_date is Jan 15, invoice generates on the 15th of every month
            if ($lease->start_date->day === $today->day) {
                
                // Prevent duplicate invoice generation for the same month
                $existingInvoice = Invoice::where('lease_id', $lease->id)
                    ->whereMonth('created_at', $today->month)
                    ->whereYear('created_at', $today->year)
                    ->first();

                if (!$existingInvoice) {
                    $invoice = Invoice::create([
                        'lease_id' => $lease->id,
                        'amount' => $lease->monthly_rent,
                        'due_date' => $today->copy()->addDays(3), // 3 days to pay
                        'status' => 'pending',
                        'description' => 'Monthly rent for ' . $today->format('F Y'),
                    ]);

                    // Send notification to tenant
                    $lease->tenant->notify(new RentDueNotification($invoice));
                    
                    $generatedCount++;
                    $this->info("Invoice generated for Lease ID {$lease->id}");
                }
            }
        }

        $this->info("Invoice generation completed. Generated: {$generatedCount}");
        Log::info("Automated Invoices generated: {$generatedCount}");
    }
}
