<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        
        if (auth('sanctum')->check()) {
            $data['is_saved'] = $this->favorites()->where('user_id', auth('sanctum')->id())->exists();
        } else {
            $data['is_saved'] = false;
        }

        return $data;
    }
}
