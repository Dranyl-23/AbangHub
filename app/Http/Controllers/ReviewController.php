<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use App\Models\TenantReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(Request $request, Property $property): RedirectResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        /** @var \App\Models\User $user */
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

    public function storeTenantReview(Request $request, User $tenant): RedirectResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        /** @var \App\Models\User $landlord */
        $landlord = Auth::user();

        if ($landlord->user_type !== 'landlord') {
            return back()->with('error', 'Only landlords can review tenants.');
        }

        // Check if the landlord has leased a property to this tenant
        // A landlord can review a tenant if they own a property that the tenant has an active/completed lease for
        $hasLeased = $tenant->leases()
            ->whereHas('property', function ($query) use ($landlord) {
                $query->where('owner_id', $landlord->id);
            })
            ->whereIn('status', ['approved', 'active', 'completed'])
            ->exists();

        if (!$hasLeased) {
            return back()->with('error', 'You can only review tenants who have rented your properties.');
        }

        // Check if the landlord already reviewed this tenant
        $existingReview = TenantReview::where('landlord_id', $landlord->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this tenant.');
        }

        TenantReview::create([
            'landlord_id' => $landlord->id,
            'tenant_id' => $tenant->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Tenant review has been submitted successfully.');
    }
}
