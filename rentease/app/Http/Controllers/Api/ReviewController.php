<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Lease;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'tenant') {
            return response()->json(['message' => 'Only tenants can review properties.'], 403);
        }

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if tenant has a lease for this property (can be active or past)
        $hasLease = Lease::where('tenant_id', $user->id)
            ->where('property_id', $request->property_id)
            ->exists();

        if (!$hasLease) {
            return response()->json(['message' => 'You can only review properties you have rented.'], 403);
        }

        try {
            DB::table('reviews')->updateOrInsert(
                ['tenant_id' => $user->id, 'property_id' => $request->property_id],
                ['rating' => $request->rating, 'comment' => $request->comment, 'created_at' => now(), 'updated_at' => now()]
            );

            return response()->json(['message' => 'Review submitted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to submit review.'], 500);
        }
    }
}
