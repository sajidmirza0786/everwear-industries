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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for guest orders
            $table->string('uuid');
            $table->string('name');
            $table->string('email');
            $table->string('mobile');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode', 25)->nullable();
            $table->string('locality')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('address');

            $table->decimal('coupon_discount', 8, 2)->default(0.00);
            $table->decimal('manual_discount', 8, 2)->default(0.00);

            $table->decimal('subtotal_incl_gst', 8, 2)->default(0.00);
            $table->decimal('subtotal_ex_gst', 8, 2)->default(0.00);
            $table->decimal('tax_amount', 8, 2)->default(0.00);
            $table->decimal('taxable_value', 8, 2)->default(0.00);
            $table->decimal('total', 8, 2);
            
            $table->decimal('total_weight', 8, 2)->default(0.00);
            $table->decimal('shipping_charge', 8, 2)->default(0.00);
            $table->decimal('shipping_charge_gst', 8, 2)->default(0.00);

            $table->enum('status', ['pending', 'completed', 'cancelled', 'in-transit'])->default('pending');
            $table->enum('payment_method', ['prepaid', 'cod'])->default('prepaid');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
