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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_attribute_id')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 8, 2);

            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->nullOnDelete();
            $table->decimal('coupon_discount', 10, 2)->default(0)->comment('incl-GST coupon saving on this line (display only)');
 
            // ── Manual discount per item ──────────────────────────────────────
            $table->enum('manual_discount_type', ['flat', 'percent'])->nullable();
            $table->decimal('manual_discount_value', 10, 2)->nullable()->comment('raw ₹ or % value entered');
            $table->decimal('manual_discount_amount', 10, 2)->default(0)->comment('resolved incl-GST amount (display only)');
            $table->string('manual_discount_note', 255)->nullable();
 
            // ── Tax / line total (computed and stored for auditability) ───────
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('total ex-GST discount this line (coupon + manual)');
            $table->decimal('taxable_price', 10, 4)->default(0)->comment('ex-GST price per unit after discount — the GST base per unit');
            $table->decimal('tax_rate', 6, 2)->default(0)->comment('GST % captured from product at save time');
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('GST component for the whole line');
            $table->decimal('line_total', 10, 2)->default(0)->comment('taxable_price * qty + tax_amount');


            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
