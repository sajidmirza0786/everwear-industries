<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for guest users
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_attribute_id')->nullable();
            $table->string('session_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2); // Store price at the time of adding to cart
            $table->decimal('shipping_charge', 8, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};