<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuration::create([
            'id' => 2023,
            'donation_info_object' => json_encode([
                'bank_type' => ['gcash' => 'primary', 'other' => 'secondary'],
                'account_name' => 'Pastor Lorenzo',
                'account_number' => '09345634567',
            ]),
            'contact_details_object' => json_encode([
                'phone_number' => '09345673456',
                'email_address' => 'jaomconnect.info@gmail.com',
            ]),
        ]);
    }
}
