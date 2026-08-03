<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * LOW-7 FIX: Explicitly define returned fields instead of parent::toArray($request)
     * to avoid dumping internal database flags or pivot metadata.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Compute is_saved state efficiently without N+1 query (LOW-4)
        $isSaved = false;
        if (isset($this->is_saved)) {
            $isSaved = (bool) $this->is_saved;
        } elseif ($this->relationLoaded('favorites')) {
            $userId = auth('sanctum')->id();
            $isSaved = $userId ? $this->favorites->contains('user_id', $userId) : false;
        } elseif (auth('sanctum')->check()) {
            $isSaved = $this->favorites()->where('user_id', auth('sanctum')->id())->exists();
        }

        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'monthly_rent'   => (float) $this->monthly_rent,
            'city'           => $this->city,
            'barangay'       => $this->barangay,
            'address'        => $this->address,
            'property_type'  => $this->property_type,
            'bedrooms'       => (int) $this->bedrooms,
            'bathrooms'      => (int) $this->bathrooms,
            'latitude'       => $this->latitude ? (float) $this->latitude : null,
            'longitude'      => $this->longitude ? (float) $this->longitude : null,
            'status'         => $this->status,
            'is_saved'       => $isSaved,
            'average_rating' => (float) ($this->average_rating ?? 0),
            'review_count'   => (int) ($this->review_count ?? 0),
            'images'         => PropertyImageResource::collection($this->whenLoaded('images')),
            'primary_image'  => $this->whenLoaded('primaryImage'),
            'amenities'      => $this->whenLoaded('amenities'),
            'owner'          => new UserResource($this->whenLoaded('owner')),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
