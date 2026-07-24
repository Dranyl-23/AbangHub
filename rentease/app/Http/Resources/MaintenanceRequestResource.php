<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'urgency' => $this->urgency,
            'status' => $this->status,
            'image' => $this->image,
            'cost' => $this->cost,
            'user' => new UserResource($this->whenLoaded('user')),
            'property' => new PropertyResource($this->whenLoaded('property')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
