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
        Schema::create('shipping_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id')->nullable(); // e.g. 'US', 'IN'
            $table->unsignedBigInteger('state_id')->nullable();   // e.g. 'CA', 'MH'
            
            $table->decimal('min_weight', 8, 2)->default(0); // in gram
            $table->decimal('max_weight', 8, 2)->nullable(); // null = no upper limit
            
            $table->decimal('min_order_amount', 10, 2)->default(0); // optional
            $table->decimal('max_order_amount', 10, 2)->nullable(); // optional
            
            $table->decimal('charge', 10, 2); // shipping cost
            
            $table->timestamps();
            // Foreign key constraint (defined separately)
            $table->foreign('country_id')
                  ->references('id')
                  ->on('countries')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Foreign key constraint (defined separately)
            $table->foreign('state_id')
                  ->references('id')
                  ->on('states')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_charges');
    }
};
