<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MaintenanceRequestController extends Controller
{
    // Tenant: List their requests
    // Landlord: List requests for their properties
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->user_type === 'tenant') {
            $requests = MaintenanceRequest::where('user_id', $user->id)
                ->with('property')
                ->latest()
                ->get();
            return view('tenant.maintenance.index', compact('requests'));
        } elseif ($user->user_type === 'landlord') {
            $propertyIds = Property::where('owner_id', $user->id)->pluck('id');
            $requests = MaintenanceRequest::whereIn('property_id', $propertyIds)
                ->with(['property', 'user'])
                ->latest()
                ->get();
            return view('landlord.maintenance.index', compact('requests'));
        }

        abort(403);
    }

    // Tenant: Show create form
    public function create(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->user_type !== 'tenant') {
            abort(403, 'Only tenants can create maintenance requests.');
        }

        // Get properties where the tenant has an approved or active lease
        $leasedPropertyIds = Lease::where('tenant_id', Auth::id())
            ->whereIn('status', ['approved', 'active'])
            ->pluck('property_id');
            
        // Fallback: If no leases, get properties where they have an approved application
        if ($leasedPropertyIds->isEmpty()) {
            $leasedPropertyIds = \App\Models\Application::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->pluck('property_id');
        }

        $properties = Property::whereIn('id', $leasedPropertyIds)->get();

        return view('tenant.maintenance.create', compact('properties'));
    }

    // Tenant: Store request
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->user_type !== 'tenant') {
            abort(403);
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $maintenanceRequest = new MaintenanceRequest();
        $maintenanceRequest->user_id = Auth::id();
        $maintenanceRequest->property_id = $validated['property_id'];
        $maintenanceRequest->title = $validated['title'];
        $maintenanceRequest->description = $validated['description'];
        $maintenanceRequest->status = 'pending';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('maintenance_images');
            $maintenanceRequest->image_path = $path;
        }

        $maintenanceRequest->save();

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'Maintenance request submitted successfully.');
    }

    // Landlord: Update status
    public function update(Request $request, MaintenanceRequest $maintenance): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->user_type !== 'landlord') {
            abort(403);
        }

        // Verify ownership
        if ($maintenance->property->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $maintenance->update([
            'status' => $validated['status'],
            'cost' => $validated['cost'] ?? 0,
        ]);

        return back()->with('success', 'Maintenance request status updated.');
    }
}
