<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'balance', 'api_key', 'referral_code',
        'referred_by', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token', 'api_key'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'balance'           => 'decimal:4',
        'is_active'         => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (User $user) {
            $user->api_key       = Str::random(32);
            $user->referral_code = strtoupper(Str::random(8));
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────────
    public function orders()       { return $this->hasMany(Order::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function payments()     { return $this->hasMany(Payment::class); }
    public function tickets()      { return $this->hasMany(Ticket::class); }
    public function referrer()     { return $this->belongsTo(User::class, 'referred_by'); }
    public function referrals()    { return $this->hasMany(User::class, 'referred_by'); }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function isAdmin():    bool { return $this->role === 'admin'; }
    public function isReseller(): bool { return $this->role === 'reseller'; }

    public function deductBalance(float $amount): void
    {
        if ($this->balance < $amount) {
            throw new \Exception('Solde insuffisant.');
        }
        $this->decrement('balance', $amount);
    }

    public function addBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }
}
