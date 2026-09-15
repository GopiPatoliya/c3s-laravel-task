<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        $statuses = ['new', 'contacted', 'converted', 'lost'];
        $sources = ['website', 'referral', 'social_media', 'cold_call', 'other'];

        for ($i = 1; $i <= 50; $i++) {
            $userId = rand(2, 6);
            \App\Models\Lead::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => substr($faker->e164PhoneNumber, 0, 15),
                'company_name' => $faker->company,
                'status' => $statuses[array_rand($statuses)],
                'source' => $sources[array_rand($sources)],
                'assigned_to' => $userId,
                'created_by' => $userId, 
                'notes' => $faker->realText(100),
            ]);
        }
    }
}
