<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lease_id' => $this->lease_id,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'description' => $this->description,
            'lease' => new LeaseResource($this->whenLoaded('lease')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
