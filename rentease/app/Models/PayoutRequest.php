<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayoutRequest extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'amount', 'method', 'account_name', 'account_number', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
