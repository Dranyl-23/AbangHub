<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'amount',
        'due_date',
        'status',
        'description',
        'receipt_image',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
