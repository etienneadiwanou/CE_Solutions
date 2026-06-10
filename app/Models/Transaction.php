<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount',
        'balance_before', 'balance_after',
        'description', 'reference_id', 'reference_type', 'status',
    ];

    protected $casts = [
        'amount'         => 'decimal:4',
        'balance_before' => 'decimal:4',
        'balance_after'  => 'decimal:4',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
