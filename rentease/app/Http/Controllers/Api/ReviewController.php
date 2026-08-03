<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Lease;

class ReviewController extends Controller
{
    /**
     * Store or update a review.
     * LOW-6 FIX: Use Review::updateOrCreate() instead of raw DB::table()
     * to trigger Eloquent model events and preserve observer hooks.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'tenant') {
            return response()->json(['message' => 'Only tenants can review properties.'], 403);
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string|max:1000',
        ]);

        // Check if tenant has a lease for this property
        $hasLease = Lease::where('tenant_id', $user->id)
            ->where('property_id', $validated['property_id'])
            ->exists();

        if (!$hasLease) {
            return response()->json(['message' => 'You can only review properties you have rented.'], 403);
        }

        try {
            // LOW-5 FIX: Sanitize user comment text
            $comment = isset($validated['comment']) ? strip_tags($validated['comment']) : null;

            $review = Review::updateOrCreate(
                [
                    'tenant_id'   => $user->id,
                    'property_id' => $validated['property_id'],
                ],
                [
                    'rating'  => $validated['rating'],
                    'comment' => $comment,
                ]
            );

            return response()->json([
                'message' => 'Review submitted successfully.',
                'review'  => $review,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to submit review.'], 500);
        }
    }
}
