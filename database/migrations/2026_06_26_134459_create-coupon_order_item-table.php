<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupon_order_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->decimal('discount_applied', 10, 2)->default(0)->comment('ex-GST discount amount saved at time of application');
            $table->unique(['coupon_id', 'order_item_id'], 'unique_coupon_order_item');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_order_item', function (Blueprint $table) {
            Schema::dropIfExists('coupon_order_item');
        });
    }
};
