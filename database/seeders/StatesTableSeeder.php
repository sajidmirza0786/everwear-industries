<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Country;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            ['name' => 'Andhra Pradesh', 'state_code' => 'AP', 'state_number' => '37', 'status' => 'enable'],
            ['name' => 'Arunachal Pradesh', 'state_code' => 'AR', 'state_number' => '03', 'status' => 'enable'],
            ['name' => 'Assam', 'state_code' => 'AS', 'state_number' => '18', 'status' => 'enable'],
            ['name' => 'Bihar', 'state_code' => 'BR', 'state_number' => '10', 'status' => 'enable'],
            ['name' => 'Chhattisgarh', 'state_code' => 'CG', 'state_number' => '22', 'status' => 'enable'],
            ['name' => 'Goa', 'state_code' => 'GA', 'state_number' => '30', 'status' => 'enable'],
            ['name' => 'Gujarat', 'state_code' => 'GJ', 'state_number' => '24', 'status' => 'enable'],
            ['name' => 'Haryana', 'state_code' => 'HR', 'state_number' => '12', 'status' => 'enable'],
            ['name' => 'Himachal Pradesh', 'state_code' => 'HP', 'state_number' => '02', 'status' => 'enable'],
            ['name' => 'Jharkhand', 'state_code' => 'JH', 'state_number' => '20', 'status' => 'enable'],
            ['name' => 'Karnataka', 'state_code' => 'KA', 'state_number' => '29', 'status' => 'enable'],
            ['name' => 'Kerala', 'state_code' => 'KL', 'state_number' => '32', 'status' => 'enable'],
            ['name' => 'Madhya Pradesh', 'state_code' => 'MP', 'state_number' => '23', 'status' => 'enable'],
            ['name' => 'Maharashtra', 'state_code' => 'MH', 'state_number' => '27', 'status' => 'enable'],
            ['name' => 'Manipur', 'state_code' => 'MN', 'state_number' => '14', 'status' => 'enable'],
            ['name' => 'Meghalaya', 'state_code' => 'ML', 'state_number' => '17', 'status' => 'enable'],
            ['name' => 'Mizoram', 'state_code' => 'MZ', 'state_number' => '15', 'status' => 'enable'],
            ['name' => 'Nagaland', 'state_code' => 'NL', 'state_number' => '13', 'status' => 'enable'],
            ['name' => 'Odisha', 'state_code' => 'OD', 'state_number' => '21', 'status' => 'enable'],
            ['name' => 'Punjab', 'state_code' => 'PB', 'state_number' => '03', 'status' => 'enable'],
            ['name' => 'Rajasthan', 'state_code' => 'RJ', 'state_number' => '08', 'status' => 'enable'],
            ['name' => 'Sikkim', 'state_code' => 'SK', 'state_number' => '11', 'status' => 'enable'],
            ['name' => 'Tamil Nadu', 'state_code' => 'TN', 'state_number' => '33', 'status' => 'enable'],
            ['name' => 'Telangana', 'state_code' => 'TG', 'state_number' => '36', 'status' => 'enable'],
            ['name' => 'Tripura', 'state_code' => 'TR', 'state_number' => '16', 'status' => 'enable'],
            ['name' => 'Uttar Pradesh', 'state_code' => 'UP', 'state_number' => '09', 'status' => 'enable'],
            ['name' => 'Uttarakhand', 'state_code' => 'UK', 'state_number' => '05', 'status' => 'enable'],
            ['name' => 'West Bengal', 'state_code' => 'WB', 'state_number' => '19', 'status' => 'enable'],

            // Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'state_code' => 'AN', 'state_number' => '35', 'status' => 'enable'],
            ['name' => 'Chandigarh', 'state_code' => 'CH', 'state_number' => '04', 'status' => 'enable'],
            ['name' => 'Dadra and Nagar Haveli and Daman & Diu', 'state_code' => 'DD', 'state_number' => '26', 'status' => 'enable'],
            ['name' => 'Lakshadweep', 'state_code' => 'LD', 'state_number' => '31', 'status' => 'enable'],
            ['name' => 'Delhi', 'state_code' => 'DL', 'state_number' => '07', 'status' => 'enable'],
            ['name' => 'Puducherry', 'state_code' => 'PY', 'state_number' => '34', 'status' => 'enable'],
            ['name' => 'Jammu and Kashmir', 'state_code' => 'JK', 'state_number' => '01', 'status' => 'enable'],
            ['name' => 'Ladakh', 'state_code' => 'LA', 'state_number' => '38', 'status' => 'enable'],
        ];


        $now = Carbon::now();

        $countryData = [
            'country_code' => 'IN',
            'numeric_code' => '+91', 
            'name' => 'India',
        ];

        $country = Country::updateOrCreate($countryData, $countryData);

        // Prepare data with timestamps
        $dataToUpsert = collect($states)->map(function ($state) use ($now, $country) {
            return array_merge($state, [
                'country_id' => $country->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->all();

        // Use upsert to insert or update records
        DB::table('states')->upsert(
            $dataToUpsert,
            ['state_code', 'country_id'], // Unique by state_code and country_id
            ['name', 'state_number', 'status', 'updated_at'] // Columns to update if record exists
        );
    }
}