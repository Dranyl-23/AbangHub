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
                ->where('tenant_id', $user->id)
                ->latest()
                ->get();
        } else {
            // If landlord, list applications for their properties
            $applications = Application::with(['property', 'tenant'])
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
            ->where('tenant_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already applied for this property.'], 422);
        }

        $application = Application::create([
            'property_id' => $property->id,
            'tenant_id' => $user->id,
            'message' => $request->message,
            'status' => 'pending',
            // Ideally we'd store move_in_date and occupants too if the migration supports it, 
            // assuming the basic application schema for now.
        ]);

        return response()->json([
            'message' => 'Application submitted successfully!',
            'application' => new ApplicationResource($application)
        ], 201);
    }
}
