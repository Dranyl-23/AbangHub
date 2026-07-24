<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'reference_number' => $this->reference_number,
            'notes' => $this->notes,
            'user' => new UserResource($this->whenLoaded('user')),
            'property' => new PropertyResource($this->whenLoaded('property')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
