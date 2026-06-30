<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_at'          => 'datetime',
        'expires_at'         => 'datetime',
        'is_active'          => 'boolean',
        'discount_value'     => 'float',
        'max_discount_amount'=> 'float',
        'min_order_amount'   => 'float',
        'max_order_amount'   => 'float',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'coupon_order')
                    ->withPivot('discount_applied')
                    ->withTimestamps();
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Check if coupon is currently valid (active, within date window, not exhausted).
     */
    public function isValid(): bool
    {
        if (! $this->is_active) return false;
        if ($this->starts_at  && now()->lt($this->starts_at))  return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->max_uses   && $this->used_count >= $this->max_uses) return false;

        return true;
    }

    /**
     * Calculate actual ₹ discount for a given order subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->min_order_amount && $subtotal < $this->min_order_amount) return 0;
        if ($this->max_order_amount && $subtotal > $this->max_order_amount) return 0;

        if ($this->discount_type === 'flat') {
            return min($this->discount_value, $subtotal);
        }

        // percent
        $discount = ($subtotal * $this->discount_value) / 100;

        if ($this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return round($discount, 2);
    }
}