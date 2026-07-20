<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Property $property)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        if ($user->user_type !== 'tenant') {
            return back()->with('error', 'Only tenants can leave a review.');
        }

        // Check if the user has rented this property
        $hasRented = $user->leases()
            ->where('property_id', $property->id)
            ->whereIn('status', ['approved', 'active', 'completed'])
            ->exists();

        if (!$hasRented) {
            return back()->with('error', 'You can only review properties you have rented.');
        }

        // Check if the user already reviewed this property
        $existingReview = Review::where('tenant_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this property.');
        }

        Review::create([
            'tenant_id' => $user->id,
            'property_id' => $property->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Your review has been submitted successfully.');
    }
}
