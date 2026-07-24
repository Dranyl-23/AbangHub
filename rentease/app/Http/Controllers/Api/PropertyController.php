<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of available properties.
     */
    public function index(Request $request)
    {
        $query = Property::with(['primaryImage', 'owner'])
            ->when(auth('sanctum')->check(), function ($q) {
                $q->withExists(['favorites as is_saved' => function ($q) {
                    $q->where('user_id', auth('sanctum')->id());
                }]);
            })
            ->where('status', 'available')
            ->where('is_banned', false);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        $properties = $query->latest()->paginate(15);

        return PropertyResource::collection($properties);
    }

    /**
     * Display the specified property.
     */
    public function show($id)
    {
        $property = Property::with(['images', 'owner', 'amenities', 'reviews.tenant'])
            ->where('is_banned', false)
            ->findOrFail($id);

        return new PropertyResource($property);
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'monthly_rent' => 'required|numeric|min:0',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'address' => 'required|string',
            'property_type' => 'required|string|in:apartment,house,condo,room,boarding_house',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'amenities' => 'nullable|string', // Will be parsed as JSON array
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $property = Property::create([
            'owner_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'monthly_rent' => $validated['monthly_rent'],
            'city' => $validated['city'],
            'barangay' => $validated['barangay'],
            'address' => $validated['address'],
            'property_type' => $validated['property_type'],
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => 'available',
        ]);

        if (!empty($validated['amenities'])) {
            $amenitiesArray = json_decode($validated['amenities'], true);
            if (is_array($amenitiesArray)) {
                $amenitiesData = array_map(function ($amenity) {
                    return ['amenity_name' => $amenity];
                }, $amenitiesArray);
                $property->amenities()->createMany($amenitiesData);
            }
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('properties', 'public');
            $property->images()->create([
                'image_path' => '/storage/' . $path,
                'is_primary' => true,
            ]);
        }

        return response()->json([
            'message' => 'Property created successfully!',
            'property' => new PropertyResource($property->load(['images', 'amenities']))
        ], 201);
    }

    /**
     * Update the specified property in storage.
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // If it's just a status update
        if ($request->has('status') && count($request->all()) === 1) {
            $request->validate(['status' => 'required|in:available,rented,maintenance']);
            $property->update(['status' => $request->status]);
            return response()->json(['message' => 'Status updated successfully', 'property' => new PropertyResource($property)], 200);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'monthly_rent' => 'sometimes|required|numeric|min:0',
            'city' => 'sometimes|required|string|max:255',
            'barangay' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'property_type' => 'sometimes|required|string|in:apartment,house,condo,room,boarding_house,studio',
            'bedrooms' => 'sometimes|required|integer|min:0',
            'bathrooms' => 'sometimes|required|integer|min:0',
            'amenities' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $property->update($request->except(['image', 'amenities']));

        if ($request->has('amenities')) {
            $property->amenities()->delete(); // clear old
            if (!empty($validated['amenities'])) {
                $amenitiesArray = json_decode($validated['amenities'], true);
                if (is_array($amenitiesArray)) {
                    $amenitiesData = array_map(function ($amenity) {
                        return ['amenity_name' => $amenity];
                    }, $amenitiesArray);
                    $property->amenities()->createMany($amenitiesData);
                }
            }
        }

        if ($request->hasFile('image')) {
            // Can optionally delete the old image here
            $path = $request->file('image')->store('properties', 'public');
            $property->images()->create([
                'image_path' => '/storage/' . $path,
                'is_primary' => true,
            ]);
        }

        return response()->json([
            'message' => 'Property updated successfully!',
            'property' => new PropertyResource($property->load(['images', 'amenities']))
        ], 200);
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->owner_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully'], 200);
    }

    /**
     * Display a listing of properties owned by the authenticated landlord.
     */
    public function landlordIndex(Request $request)
    {
        $properties = Property::with(['primaryImage', 'owner'])
            ->where('owner_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return PropertyResource::collection($properties);
    }
}
