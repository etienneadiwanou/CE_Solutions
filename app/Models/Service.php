<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description',
        'price_per_1000', 'min_quantity', 'max_quantity',
        'type', 'provider_id', 'provider_service_id',
        'refill', 'cancel', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price_per_1000' => 'decimal:4',
        'refill'         => 'boolean',
        'cancel'         => 'boolean',
        'is_active'      => 'boolean',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function provider() { return $this->belongsTo(Provider::class); }
    public function orders()   { return $this->hasMany(Order::class); }

    /** Calcule le prix total pour une quantité donnée */
    public function calculateCharge(int $quantity): float
    {
        return round(($this->price_per_1000 / 1000) * $quantity, 4);
    }
}
