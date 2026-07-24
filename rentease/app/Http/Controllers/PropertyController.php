<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->user_type === 'landlord') {
            return view('properties.manage');
        }

        // For tenants, return the search view
        return view('properties.index');
    }

    public function show(Property $property): View
    {
        $property->load(['images', 'amenities', 'owner', 'reviews.tenant']);
        return view('properties.show', compact('property'));
    }

    public function create(): View
    {
        return view('properties.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'property_type' => 'required|in:apartment,house,condo,room,boarding_house',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'floor_area' => 'nullable|numeric|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'barangay' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:available,rented,maintenance',
            'furnishing_status' => 'required|in:furnished,semi_furnished,unfurnished',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB per image
        ]);

        $validated['owner_id'] = Auth::id();
        
        $property = Property::create($validated);

        // Handle Image Uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0 // First image is primary
                ]);
            }
        }

        return redirect()->route('properties.index')
            ->with('success', 'Property created successfully!');
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);
        $property->load(['images', 'amenities']);
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'property_type' => 'required|in:apartment,house,condo,room,boarding_house',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'floor_area' => 'nullable|numeric|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'barangay' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:available,rented,maintenance',
            'furnishing_status' => 'required|in:furnished,semi_furnished,unfurnished',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $property->update($validated);

        // Handle Image Uploads (Appending new ones)
        if ($request->hasFile('images')) {
            $hasPrimary = $property->images()->where('is_primary', true)->exists();
            
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'is_primary' => !$hasPrimary && $index === 0
                ]);
            }
        }

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);
        
        // Delete images from storage
        foreach ($property->images as $image) {
            Storage::delete($image->image_path);
        }
        
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully!');
    }

    public function toggleFavorite(Request $request, Property $property)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $favorite = \App\Models\Favorite::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
            $message = 'Property removed from favorites.';
        } else {
            \App\Models\Favorite::create([
                'user_id' => $user->id,
                'property_id' => $property->id
            ]);
            $status = 'added';
            $message = 'Property saved to favorites.';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }
}
