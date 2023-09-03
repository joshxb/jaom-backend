<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\FAQSDataSeeder;
use Database\Seeders\UserDataSeeder;
use Database\Seeders\ConfigurationSeeder;

class FactoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(FAQSDataSeeder::class);
        $this->call(UserDataSeeder::class);
        $this->call(ConfigurationSeeder::class);
    }
}
