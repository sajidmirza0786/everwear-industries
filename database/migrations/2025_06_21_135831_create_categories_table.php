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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable(); // Make it nullable to allow root categories
            $table->string('name');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->string('hsn')->nullable();
            $table->string('title')->nullable();
            $table->string('keyword')->nullable();
            $table->string('description')->nullable();
            $table->text('long_description')->nullable();
            $table->enum('status', ['enable', 'disable'])->default('enable');
            $table->softDeletes();
            $table->timestamps();

            // Define foreign key separately
            $table->foreign('parent_id')
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
        Schema::dropIfExists('categories');
    }
};
