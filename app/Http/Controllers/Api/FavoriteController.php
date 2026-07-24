<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Property;
use App\Http\Resources\PropertyResource;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's saved properties.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Fetch properties that are favorited by the user
        $properties = Property::with(['primaryImage', 'owner'])
            ->whereHas('favorites', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', 'available')
            ->where('is_banned', false)
            ->latest()
            ->paginate(15);

        return PropertyResource::collection($properties);
    }

    /**
     * Toggle the saved status of a property.
     */
    public function toggle(Request $request, $id)
    {
        $user = $request->user();
        
        $property = Property::findOrFail($id);

        $favorite = Favorite::where('user_id', $user->id)
                            ->where('property_id', $property->id)
                            ->first();

        if ($favorite) {
            // If already favorited, remove it
            $favorite->delete();
            return response()->json([
                'message' => 'Property removed from saved list.',
                'is_saved' => false
            ]);
        } else {
            // If not favorited, add it
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $property->id
            ]);
            return response()->json([
                'message' => 'Property saved successfully.',
                'is_saved' => true
            ]);
        }
    }
}
