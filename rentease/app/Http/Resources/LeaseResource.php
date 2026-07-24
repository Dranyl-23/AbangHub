<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'property_id' => $this->property_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'monthly_rent' => $this->monthly_rent,
            'status' => $this->status,
            'tenant' => new UserResource($this->whenLoaded('tenant')),
            'property' => new PropertyResource($this->whenLoaded('property')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
