<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['lease.property'])
            ->whereHas('lease', function ($query) {
                $query->where('tenant_id', auth()->id());
            })
            ->latest()
            ->get();
            
        return view('tenant.invoices.index', compact('invoices'));
    }

    public function pay(Request $request, Invoice $invoice)
    {
        // Ensure user owns this invoice
        if (auth()->id() !== $invoice->lease->tenant_id) {
            abort(403);
        }

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'This invoice is already paid.');
        }

        // Validate mock payment data
        $validated = $request->validate([
            'payment_method' => 'required|in:card,gcash',
            'amount' => 'required|numeric'
        ]);

        // Process payment (Simulated)
        $invoice->update([
            'status' => 'paid',
        ]);

        // Create transaction record
        Transaction::create([
            'user_id' => auth()->id(),
            'property_id' => $invoice->lease->property_id,
            'type' => 'payment',
            'amount' => $invoice->amount,
            'status' => 'completed',
            'reference_number' => strtoupper(Str::random(10)),
            'description' => 'Payment for: ' . $invoice->description
        ]);

        // Add to landlord's wallet (minus a hypothetical 5% platform fee)
        $landlord = $invoice->lease->property->owner;
        $platformFee = $invoice->amount * 0.05;
        $netPayout = $invoice->amount - $platformFee;

        $wallet = $landlord->wallet ?? $landlord->wallet()->create(['balance' => 0]);
        $wallet->increment('balance', $netPayout);

        // If this was the initial rent payment, update the property status to rented
        if ($invoice->lease->property->status !== 'rented') {
            $invoice->lease->property->update(['status' => 'rented']);
        }

        return redirect()->route('tenant.invoices.index')->with('success', 'Payment successful! Thank you.');
    }
}
