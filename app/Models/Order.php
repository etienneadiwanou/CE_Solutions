<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'service_id', 'link', 'quantity',
        'charge', 'start_count', 'remains', 'status',
        'provider_order_id', 'provider_id',
        'refill_requested', 'completed_at',
    ];

    protected $casts = [
        'charge'           => 'decimal:4',
        'refill_requested' => 'boolean',
        'completed_at'     => 'datetime',
    ];

    // Statuts possibles
    const STATUS_PENDING     = 'pending';
    const STATUS_PROCESSING  = 'processing';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_PARTIAL     = 'partial';
    const STATUS_CANCELLED   = 'cancelled';
    const STATUS_FAILED      = 'failed';
    const STATUS_REFUNDED    = 'refunded';

    public function user()     { return $this->belongsTo(User::class); }
    public function service()  { return $this->belongsTo(Service::class); }
    public function provider() { return $this->belongsTo(Provider::class); }

    public function isCompleted(): bool { return $this->status === self::STATUS_COMPLETED; }
    public function isFailed():    bool { return in_array($this->status, [self::STATUS_FAILED, self::STATUS_CANCELLED]); }
}
