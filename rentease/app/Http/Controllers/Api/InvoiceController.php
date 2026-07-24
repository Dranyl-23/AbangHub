<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lease;
use Illuminate\Http\Request;
use App\Http\Resources\InvoiceResource;

class InvoiceController extends Controller
{
    /**
     * Get all invoices for the landlord's leases.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type === 'landlord') {
            $invoices = Invoice::with(['lease', 'lease.tenant', 'lease.property'])
                ->whereHas('lease.property', function ($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })
                ->latest()
                ->get();
        } elseif ($user->user_type === 'tenant') {
            $invoices = Invoice::with(['lease', 'lease.property'])
                ->whereHas('lease', function ($query) use ($user) {
                    $query->where('tenant_id', $user->id);
                })
                ->latest()
                ->get();
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return InvoiceResource::collection($invoices);
    }

    /**
     * Store a new invoice.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'landlord') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        // Ensure the lease belongs to a property owned by the landlord
        $lease = Lease::with('property')->findOrFail($request->lease_id);
        if ($lease->property->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invoice = Invoice::create([
            'lease_id' => $request->lease_id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Invoice created successfully.',
            'invoice' => new InvoiceResource($invoice->load(['lease', 'lease.tenant', 'lease.property']))
        ], 201);
    }

    /**
     * Update invoice status (e.g. mark as paid).
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'status' => 'required|in:pending,paid,overdue',
            'receipt_image' => 'nullable|image|max:5120' // 5MB max
        ]);

        $invoice = Invoice::with('lease.property')->findOrFail($id);

        // Ensure the invoice belongs to a property owned by the landlord or the tenant paying it
        if ($user->user_type === 'landlord' && $invoice->lease->property->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($user->user_type === 'tenant' && $invoice->lease->tenant_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = ['status' => $request->status];

        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts');
            $data['receipt_image'] = $path;
            
            // If tenant is uploading, they might change status to 'pending_verification' or similar
            // But we'll just allow them to set it to 'paid' for now based on validation rule
        }

        $invoice->update($data);

        return response()->json([
            'message' => 'Invoice status updated.',
            'invoice' => new InvoiceResource($invoice->load(['lease', 'lease.tenant', 'lease.property']))
        ]);
    }
}
