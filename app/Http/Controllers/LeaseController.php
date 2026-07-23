<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeaseController extends Controller
{
    public function sign(Lease $lease): View|RedirectResponse
    {
        if (Auth::id() !== $lease->tenant_id) {
            abort(403);
        }

        if ($lease->status !== 'pending_signature') {
            return redirect()->route('tenant.applications.index')->with('error', 'Lease is already signed or not available.');
        }

        return view('tenant.leases.sign', compact('lease'));
    }

    public function processSignature(Request $request, Lease $lease): RedirectResponse
    {
        if (Auth::id() !== $lease->tenant_id) {
            abort(403);
        }

        if ($lease->status !== 'pending_signature') {
            return redirect()->back()->with('error', 'Lease is already signed.');
        }

        $validated = $request->validate([
            'signature' => 'required|string', // Base64 image data URL
        ]);

        $lease->update([
            'tenant_signature' => $validated['signature'],
            'signed_at' => now(),
            'status' => 'active',
        ]);

        return redirect()->route('tenant.invoices.index')->with('success', 'Lease signed successfully! Please pay your initial invoice to complete the move-in process.');
    }

    public function downloadContract(Lease $lease)
    {
        // Only tenant or landlord can download
        if (Auth::id() !== $lease->tenant_id && Auth::id() !== $lease->property->owner_id) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.lease-contract', compact('lease'));
        
        return $pdf->download('lease-contract-' . $lease->id . '.pdf');
    }
}
