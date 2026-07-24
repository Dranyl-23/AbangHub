<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\Request;
use App\Http\Resources\LeaseResource;

class LeaseController extends Controller
{
    /**
     * Get all leases for the landlord's properties.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'landlord') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $leases = Lease::with(['tenant', 'property'])
            ->whereHas('property', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->latest()
            ->get();

        return LeaseResource::collection($leases);
    }
}
