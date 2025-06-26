<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('home_pages')->truncate(); // Ensure clean state

        DB::table('home_pages')->insert([
            'title' => 'The Onjewel',
            'keywords' => 'online shop, ecommerce, products',
            'description' => 'Welcome to the best online shopping experience. The Onjewel',
            'logo' => 'homepage/logo.png',
            'favicon' => 'homepage/favicon.png',
            'banner_image_1' => 'homepage/banner1.jpg',
            'banner_image_2' => 'homepage/banner2.jpg',
            'banner_image_3' => 'homepage/banner3.jpg',
            'mobile' => '+918510047947',
            'alt_mobile' => '',
            'email' => 'query@onjewel.co.in',
            'alt_email' => '',
            'address' => '123 Main Street, New Delhi, India',
            'short_about_description' => 'Online Shopping has always been a fun and exciting task for most and more so when the shopping mall is none other than your own house.',
                'long_about_description' => 'Welcome to Jewel, a small business founded in 2016, specializing in offering a curated collection of fine artificial jewelry at affordable prices. Our company is dedicated to bringing you the latest trends and styles in jewelry from various parts of the world.

                While we do not produce the jewelry ourselves, we take pride in sourcing our products from renowned manufacturers in countries like China, India, and Korea. These regions are known for their rich heritage in jewelry-making and craftsmanship. We have established strong partnerships with trusted suppliers who share our commitment to quality and design.

                At Jewel, we understand that jewellery is a form of self-expression and a way to enhance your personal style. Our diverse range of products includes stunning necklaces, earrings, bracelets, and more, carefully selected to cater to different tastes and preferences.

                Customer satisfaction is of utmost importance to us, and we strive to provide a seamless shopping experience. Our website is designed to be user-friendly, allowing you to browse through our collection and place orders with ease. We also have a dedicated customer service team ready to assist you with any inquiries or concerns you may have.

                Thank you for choosing Jewel as your go-to destination for affordable and fashionable artificial jewellery. We look forward to helping you find the perfect pieces to complement your unique style.',

            'heading' => 'The Onjewel',
            'other_image_1' => 'homepage/feature1.jpg',
            'other_image_2' => 'homepage/feature2.jpg',
            'facebook' => 'https://www.facebook.com/jewelonjewel?mibextid=ZbWKwL',
            'instagram' => 'https://www.instagram.com/theonjewel_/',
            'linkedin' => '',
            'twitter' => '',
            'youtube' => 'https://www.youtube.com/@onjewel2051',
            'footer_text' => '© ' . date('Y') . ' On Jewel. All Rights Reserved. Designed by ',
            'show_banners' => true,
            'show_about_section' => true,
            'show_testimonials' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
