<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();

            // General Meta Information
            $table->string('title')->nullable();
            $table->string('keywords')->nullable();
            $table->string('description')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();

            // Contact Information
            $table->string('mobile')->nullable();
            $table->string('alt_mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('alt_email')->nullable();
            $table->string('address')->nullable();

            // About Section
            $table->string('heading')->nullable();
            $table->string('short_about_description')->nullable();
            $table->text('long_about_description')->nullable();

            // Banner Images (nullable and optional)
            $table->string('banner_image_1')->nullable();
            $table->string('banner_image_2')->nullable();
            $table->string('banner_image_3')->nullable();

            // Other Images (marketing, testimonials, etc.)
            $table->string('other_image_1')->nullable();
            $table->string('other_image_2')->nullable();

            // Section Visibility Toggles
            $table->boolean('show_banners')->default(true);
            $table->boolean('show_about_section')->default(true);
            $table->boolean('show_testimonials')->default(true);

            // Social Links
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();

            // Footer Content
            $table->text('footer_text')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_pages');
    }
};
