<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
 
            // Discount type: 'flat' = fixed ₹ amount, 'percent' = percentage off
            $table->enum('discount_type', ['flat', 'percent'])->default('flat');
            $table->decimal('discount_value', 10, 2);
 
            // Percent cap: max ₹ discount allowed when type is 'percent'
            $table->decimal('max_discount_amount', 10, 2)->nullable();
 
            // Order eligibility constraints
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_order_amount', 10, 2)->nullable();
 
            // ── Per-item targeting whitelists (null = applies to ALL items) ──
            // Stores JSON arrays of product IDs / category IDs.
            // e.g. applicable_products = [1, 5, 12]
            // e.g. applicable_categories = [3, 7]
            // If BOTH are set, item qualifies if it matches EITHER list (OR logic).
            // If BOTH are null, coupon applies to every item in the order.
            $table->json('applicable_products')->nullable();
            $table->json('applicable_categories')->nullable();
 
            // Usage constraints
            $table->integer('max_uses')->nullable();
            $table->integer('max_uses_per_user')->nullable();
            $table->integer('used_count')->default(0);
 
            // Validity
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};