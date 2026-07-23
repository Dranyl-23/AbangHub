<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandlordDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ComplianceController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $documents = $user->documents()->get()->keyBy('document_type');
        
        return view('compliance.index', compact('documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Delete old document of the same type if exists
        $existing = $user->documents()->where('document_type', $request->document_type)->first();
        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        $path = $request->file('document_file')->store('compliance_docs', 'public');

        $user->documents()->create([
            'document_type' => $request->document_type,
            'file_path' => $path,
            'status' => 'pending', 
        ]);

        return back()->with('success', 'Document uploaded successfully!');
    }

    public function approve(LandlordDocument $document): RedirectResponse
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        if ($admin->user_type !== 'admin') abort(403);

        $document->update(['status' => 'approved']);
        $document->user->update(['is_verified' => true]);

        return back()->with('success', 'Landlord verified successfully.');
    }

    public function reject(LandlordDocument $document): RedirectResponse
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        if ($admin->user_type !== 'admin') abort(403);

        $document->update(['status' => 'rejected']);
        $document->user->update(['is_verified' => false]);

        return back()->with('success', 'Document rejected.');
    }
}
