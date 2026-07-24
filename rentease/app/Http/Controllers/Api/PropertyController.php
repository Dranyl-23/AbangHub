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
        $property = Property::with(['images', 'owner', 'amenities', 'reviews.user'])
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
            'address' => 'required|string',
            'property_type' => 'required|string|in:apartment,house,room,studio',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $property = Property::create([
            'owner_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'monthly_rent' => $validated['monthly_rent'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'property_type' => $validated['property_type'],
            'status' => 'available',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('properties', 'public');
            $property->images()->create([
                'image_path' => '/storage/' . $path,
                'is_primary' => true,
            ]);
        }

        return response()->json([
            'message' => 'Property created successfully!',
            'property' => new PropertyResource($property->load('images'))
        ], 201);
    }
}
