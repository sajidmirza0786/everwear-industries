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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('slug');
            $table->string('code');
            $table->float('mrp', 8, 2);
            $table->float('selling', 8, 2);
            $table->float('gram_weight', 8, 2);
            $table->integer('stock')->default(0);
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('title')->nullable();
            $table->string('keyword')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['enable', 'disable'])->default('enable');
            $table->string('description')->nullable();
            $table->text('long_description')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Define foreign key separately
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
