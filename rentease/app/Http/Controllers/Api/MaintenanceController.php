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
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15); // HIGH-6 FIX: Paginate maintenance requests
        } else {
            $requests = MaintenanceRequest::with(['property', 'user'])
                ->whereHas('property', function ($q) use ($user) {
                    $q->where('owner_id', $user->id);
                })
                ->latest()
                ->paginate(15); // HIGH-6 FIX: Paginate maintenance requests
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
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|max:5120', // Up to 5MB image
        ]);

        $user = $request->user();

        if ($user->user_type !== 'tenant') {
            return response()->json(['message' => 'Only tenants can create maintenance requests.'], 403);
        }

        // CRIT-5 FIX: Verify the tenant has an active lease on this property.
        $hasActiveLease = \App\Models\Lease::where('tenant_id', $user->id)
            ->where('property_id', $request->property_id)
            ->where('status', 'active')
            ->exists();

        if (!$hasActiveLease) {
            return response()->json([
                'message' => 'You can only submit maintenance requests for properties you currently rent.'
            ], 403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('maintenance');
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'property_id'  => $request->property_id,
            'user_id'      => $user->id,
            'title'        => $request->title,
            'description'  => $request->description,
            'image_path'   => $imagePath,
            'status'       => 'pending',
        ]);

        return response()->json([
            'message'             => 'Maintenance request submitted successfully.',
            'maintenance_request' => new MaintenanceRequestResource($maintenanceRequest)
        ], 201);
    }

    /**
     * Update the specified maintenance request (Landlord only).
     */
    public function update(Request $request, string|int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'cost'   => 'nullable|numeric|min:0'
        ]);

        $maintenanceRequest = MaintenanceRequest::with('property')->findOrFail($id);

        if ($maintenanceRequest->property->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized. Only the property landlord can update this request.'], 403);
        }

        $updateData = ['status' => $request->status];

        if ($request->status === 'resolved' && $request->has('cost')) {
            $updateData['cost'] = $request->cost;
        }

        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('maintenance/receipts');
            $updateData['receipt_image_path'] = $path;
        }

        $maintenanceRequest->update($updateData);

        return response()->json([
            'message'             => 'Maintenance request status updated.',
            'maintenance_request' => new MaintenanceRequestResource($maintenanceRequest)
        ]);
    }
}
