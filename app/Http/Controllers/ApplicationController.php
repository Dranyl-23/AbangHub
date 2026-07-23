<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    use AuthorizesRequests;

    public function tenantIndex(): View
    {
        $applications = Application::with('property.images')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('tenant.applications.index', compact('applications'));
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        // Prevent landlord from applying to their own property
        if (Auth::id() === $property->owner_id) {
            return redirect()->back()->with('error', 'You cannot apply for your own property.');
        }

        // Prevent multiple pending applications for the same property
        $existing = Application::where('user_id', Auth::id())
            ->where('property_id', $property->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You already have an active application for this property.');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
            'move_in_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['property_id'] = $property->id;
        $validated['status'] = 'pending';

        Application::create($validated);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Update user_type to tenant if they are a guest/standard user
        if ($user->user_type === 'standard') {
            $user->update(['user_type' => 'tenant']);
        }

        return redirect()->route('tenant.applications.index')
            ->with('success', 'Your application has been submitted successfully! The landlord will review it shortly.');
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        // Only the property owner can update status
        if (Auth::id() !== $application->property->owner_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $application->update(['status' => $validated['status']]);

        if ($validated['status'] === 'approved') {
            // Check if lease already exists
            $existingLease = \App\Models\Lease::where('tenant_id', $application->user_id)
                                ->where('property_id', $application->property_id)
                                ->first();
                                
            if (!$existingLease) {
                // Create a pending lease
                $lease = \App\Models\Lease::create([
                    'tenant_id' => $application->user_id,
                    'property_id' => $application->property_id,
                    'start_date' => $application->move_in_date ?? now(),
                    'end_date' => \Carbon\Carbon::parse($application->move_in_date ?? now())->addMonths(6),
                    'monthly_rent' => $application->property->monthly_rent,
                    'status' => 'pending_signature'
                ]);

                // Create initial invoice
                $amount = $application->property->monthly_rent;
                if ($application->property->security_deposit) {
                    $amount += $application->property->security_deposit;
                }
                
                \App\Models\Invoice::create([
                    'lease_id' => $lease->id,
                    'amount' => $amount,
                    'due_date' => now(),
                    'status' => 'pending',
                    'description' => 'Initial rent and security deposit'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Application status updated to ' . $validated['status'] . '.');
    }
}
