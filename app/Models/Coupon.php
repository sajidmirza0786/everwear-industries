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

    /**
     * How many times has a specific user already used this coupon?
     */
    public function usedCountByUser(int $userId): int
    {
        return \DB::table('coupon_order_item')
            ->join('order_items', 'order_items.id', '=', 'coupon_order_item.order_item_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('coupon_order_item.coupon_id', $this->id)
            ->where('orders.user_id', $userId)
            ->whereNotIn('orders.status', ['cancelled'])
            ->count();
    }

    /**
     * Does this coupon apply to a specific product (and its category)?
     *
     * Returns true when:
     *  - Both whitelists are null (applies to everything), OR
     *  - The product ID is in applicable_products, OR
     *  - The product's category_id is in applicable_categories.
     */
    public function appliesToProduct(\App\Models\Product $product): bool
    {
        $products   = $this->applicable_products   ? json_decode($this->applicable_products,   true) : null;
        $categories = $this->applicable_categories ? json_decode($this->applicable_categories, true) : null;
    
        if ($products === null && $categories === null) return true;
    
        if ($products   !== null && in_array($product->id,          $products))   return true;
        if ($categories !== null && in_array($product->category_id, $categories)) return true;
    
        return false;
    }
}