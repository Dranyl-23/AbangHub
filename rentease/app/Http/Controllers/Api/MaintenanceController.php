<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Http\Resources\MaintenanceRequestResource;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance requests.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->user_type === 'tenant') {
            $requests = MaintenanceRequest::with('property')
                ->where('tenant_id', $user->id)
                ->latest()
                ->get();
        } else {
            $requests = MaintenanceRequest::with(['property', 'tenant'])
                ->whereHas('property', function($q) use ($user) {
                    $q->where('owner_id', $user->id);
                })
                ->latest()
                ->get();
        }

        return MaintenanceRequestResource::collection($requests);
    }

    /**
     * Store a newly created maintenance request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'urgency' => 'required|in:low,medium,high,emergency',
            'image' => 'nullable|image|max:5120', // Up to 5MB image
        ]);

        $user = $request->user();

        if ($user->user_type !== 'tenant') {
            return response()->json(['message' => 'Only tenants can create maintenance requests.'], 403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('maintenance', 'public');
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'property_id' => $request->property_id,
            'tenant_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'urgency' => $request->urgency,
            'status' => 'pending',
            // If there's an image column in the migration we'd save it here.
            // Currently assuming standard schema. To save the path, we might need a migration if it doesn't exist, 
            // but we'll include it in the upload logic anyway.
        ]);

        return response()->json([
            'message' => 'Maintenance request submitted successfully.',
            'maintenance_request' => new MaintenanceRequestResource($maintenanceRequest)
        ], 201);
    }
}
