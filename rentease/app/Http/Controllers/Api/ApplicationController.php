<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Lease;
use App\Models\Property;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the authenticated user's applications.
     * HIGH-6 FIX: Use paginate() instead of ->get() to prevent memory issues at scale.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type === 'tenant') {
            $applications = Application::with(['property.primaryImage', 'property.owner'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15); // HIGH-6 FIX: Paginated
        } else {
            // Landlord: list applications for their properties
            $applications = Application::with(['property.primaryImage', 'user'])
                ->whereHas('property', function ($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })
                ->latest()
                ->paginate(15); // HIGH-6 FIX: Paginated
        }

        return ApplicationResource::collection($applications);
    }

    /**
     * Store a newly created application in storage.
     */
    public function store(Request $request, $propertyId)
    {
        $validated = $request->validate([
            'message'      => 'required|string|max:1000',
            'move_in_date' => 'required|date|after:today',
            'occupants'    => 'required|integer|min:1',
        ]);

        $property = Property::findOrFail($propertyId);
        $user = $request->user();

        if ($user->user_type !== 'tenant') {
            return response()->json(['message' => 'Only tenants can apply for properties.'], 403);
        }

        // MED-5 FIX: Only allow applications on available properties
        if ($property->status !== 'available') {
            return response()->json([
                'message' => 'This property is no longer available for applications.'
            ], 422);
        }

        // Prevent duplicate applications
        $existing = Application::where('property_id', $property->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You have already applied for this property.'
            ], 422);
        }

        $application = Application::create([
            'user_id'      => $user->id,
            'property_id'  => $property->id,
            'message'      => $validated['message'],
            'move_in_date' => $validated['move_in_date'],
            'occupants'    => $validated['occupants'],
            'status'       => 'pending',
        ]);

        return response()->json([
            'message'     => 'Application submitted successfully!',
            'application' => new ApplicationResource($application),
        ], 201);
    }

    /**
     * Update the application status (approve/reject).
     *
     * HIGH-4 FIX: Wrapped the approve flow in a DB transaction.
     * Previously, if the server crashed between updating the application,
     * creating the lease, and marking the property as rented, the data
     * would be left in an inconsistent state (e.g., approved but no lease).
     * DB::transaction() ensures all three succeed or all are rolled back.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $application = Application::with('property')->findOrFail($id);
        $user = $request->user();

        // Only the landlord who owns the property can update the application
        if ($user->user_type !== 'landlord' || $application->property->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // HIGH-4 FIX: Wrap all state changes in a single atomic DB transaction.
        // If any step fails, the entire operation rolls back automatically.
        DB::transaction(function () use ($application, $validated) {
            $application->update(['status' => $validated['status']]);

            if ($validated['status'] === 'approved') {
                // Auto-create the lease record
                Lease::firstOrCreate(
                    [
                        'tenant_id'   => $application->user_id,
                        'property_id' => $application->property_id,
                    ],
                    [
                        'start_date'   => now()->toDateString(),
                        'end_date'     => now()->addYear()->toDateString(),
                        'monthly_rent' => $application->property->monthly_rent,
                        'status'       => 'active',
                    ]
                );

                // Mark the property as rented so no new applications can be submitted
                $application->property->update(['status' => 'rented']);

                // Reject all other pending applications for the same property
                Application::where('property_id', $application->property_id)
                    ->where('id', '!=', $application->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
            }
        });

        return response()->json([
            'message'     => 'Application status updated.',
            'application' => new ApplicationResource($application->fresh()),
        ]);
    }
}
