<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandlordDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplianceController extends Controller
{
    public function index()
    {
        $documents = Auth::user()->documents()->get()->keyBy('document_type');
        
        return view('compliance.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

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
            'status' => 'approved', // Auto-approved for demo purposes
        ]);

        return back()->with('success', 'Document uploaded successfully!');
    }
}
