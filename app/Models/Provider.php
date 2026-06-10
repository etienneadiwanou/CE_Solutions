<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'api_url', 'api_key', 'is_active', 'balance', 'priority'];
    protected $hidden   = ['api_key'];
    protected $casts    = ['is_active' => 'boolean', 'balance' => 'decimal:4'];

    public function services() { return $this->hasMany(Service::class); }
    public function orders()   { return $this->hasMany(Order::class); }
}
