<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'local_password' => 'password',
            'mobile' => '7500752436',
            'user_type' => 'admin',
            'uuid' => str()->uuid()->toString(),
        ]);

        $this->call([
            HomePageSeeder::class,
            StatesTableSeeder::class,
        ]);
    }
}
