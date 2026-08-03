<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\InvoiceResource;

class InvoiceController extends Controller
{
    /**
     * Get invoices for the authenticated user (landlord or tenant).
     * HIGH-6 FIX: Use paginate() instead of ->get() to prevent memory issues at scale.
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
                ->paginate(15); // HIGH-6 FIX: Paginated
        } elseif ($user->user_type === 'tenant') {
            $invoices = Invoice::with(['lease', 'lease.property'])
                ->whereHas('lease', function ($query) use ($user) {
                    $query->where('tenant_id', $user->id);
                })
                ->latest()
                ->paginate(15); // HIGH-6 FIX: Paginated
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return InvoiceResource::collection($invoices);
    }

    /**
     * Store a new invoice (landlord only).
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'landlord') {
            return response()->json(['message' => 'Only landlords can create invoices.'], 403);
        }

        $validated = $request->validate([
            'lease_id'    => 'required|exists:leases,id',
            'amount'      => 'required|numeric|min:0',
            'due_date'    => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        // Ensure the lease belongs to a property owned by this landlord
        $lease = Lease::with('property')->findOrFail($validated['lease_id']);
        if ($lease->property->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $invoice = Invoice::create([
            'lease_id'    => $validated['lease_id'],
            'amount'      => $validated['amount'],
            'due_date'    => $validated['due_date'],
            'description' => $validated['description'],
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Invoice created successfully.',
            'invoice' => new InvoiceResource($invoice->load(['lease', 'lease.tenant', 'lease.property']))
        ], 201);
    }

    /**
     * Update invoice status.
     *
     * HIGH-3 FIX: Role-based status restrictions.
     * - Tenants can ONLY set status to 'paid' (they are paying the invoice).
     * - Landlords can set status to 'pending', 'paid', or 'overdue'.
     * Without this check, tenants could mark any invoice as 'overdue' or reset it to 'pending',
     * bypassing payment and manipulating records.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        // HIGH-3 FIX: Determine allowed statuses based on the caller's role
        $allowedStatuses = $user->user_type === 'tenant'
            ? ['paid']                        // Tenants can only mark as paid
            : ['pending', 'paid', 'overdue']; // Landlords have full control

        $validated = $request->validate([
            'status'        => ['required', Rule::in($allowedStatuses)],
            'receipt_image' => 'nullable|image|max:5120',
        ]);

        $invoice = Invoice::with('lease.property')->findOrFail($id);

        // Ownership check: landlord must own the property, tenant must be the lessee
        if ($user->user_type === 'landlord' && $invoice->lease->property->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($user->user_type === 'tenant' && $invoice->lease->tenant_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $data = ['status' => $validated['status']];

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')->store('receipts');
        }

        // Track when the invoice was paid for accurate income reporting (fixes MED-6)
        if ($validated['status'] === 'paid' && !$invoice->paid_at) {
            $data['paid_at'] = now();
        }

        $invoice->update($data);

        return response()->json([
            'message' => 'Invoice status updated.',
            'invoice' => new InvoiceResource($invoice->load(['lease', 'lease.tenant', 'lease.property']))
        ]);
    }
}
