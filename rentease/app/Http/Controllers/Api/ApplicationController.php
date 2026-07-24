<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Property;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the authenticated user's applications.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->user_type === 'tenant') {
            $applications = Application::with(['property.primaryImage', 'property.owner'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            // If landlord, list applications for their properties
            $applications = Application::with(['property.primaryImage', 'user'])
                ->whereHas('property', function ($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })
                ->latest()
                ->get();
        }

        return ApplicationResource::collection($applications);
    }

    /**
     * Store a newly created application in storage.
     */
    public function store(Request $request, $propertyId)
    {
        $request->validate([
            'message' => 'required|string',
            'move_in_date' => 'required|date|after:today',
            'occupants' => 'required|integer|min:1',
        ]);

        $property = Property::findOrFail($propertyId);
        $user = $request->user();

        if ($user->user_type !== 'tenant') {
            return response()->json(['message' => 'Only tenants can apply for properties.'], 403);
        }

        // Check if already applied
        $existing = Application::where('property_id', $property->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already applied for this property.'], 422);
        }

        $application = Application::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'message' => $request->message,
            'move_in_date' => $request->move_in_date,
            'occupants' => $request->occupants,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Application submitted successfully!',
            'application' => new ApplicationResource($application)
        ], 201);
    }

    /**
     * Update the application status (approve/reject).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $application = Application::findOrFail($id);
        $user = $request->user();

        // Check if the user is the landlord of the property
        if ($user->user_type !== 'landlord' || $application->property->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $application->update([
            'status' => $request->status
        ]);

        if ($request->status === 'approved') {
            // Auto-create lease
            \App\Models\Lease::firstOrCreate(
                [
                    'tenant_id' => $application->user_id,
                    'property_id' => $application->property_id,
                ],
                [
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addYear()->toDateString(),
                    'monthly_rent' => $application->property->monthly_rent,
                    'status' => 'active'
                ]
            );
            
            // Optionally, update property status to rented
            $application->property->update(['status' => 'rented']);
        }

        return response()->json([
            'message' => 'Application status updated.',
            'application' => new ApplicationResource($application)
        ]);
    }
}
