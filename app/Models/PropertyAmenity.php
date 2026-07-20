<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['property_id', 'amenity_name'])]
class PropertyAmenity extends Model
{
    use HasFactory;

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
