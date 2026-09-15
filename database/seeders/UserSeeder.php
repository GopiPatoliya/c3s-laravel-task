<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $faker = \Faker\Factory::create();
        
        // 5 Staff Users for realistic assignment testing
        for ($i = 1; $i <= 5; $i++) {
            \App\Models\User::create([
                'name' => $faker->name,
                'email' => 'staff' . $i . '@example.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]);
        }
    }
}
