<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['tenant_id', 'property_id', 'rating', 'comment'])]
class Review extends Model
{
    use HasFactory;

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
