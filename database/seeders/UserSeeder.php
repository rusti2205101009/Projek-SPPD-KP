<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'nip_nipppk' => '111111111111111111',
            'password' => bcrypt('admin123'), 
            'role' => 'admin',
        ]);

        User::factory()->create([
            'nip_nipppk' => '222222222222222222',
            'password' => bcrypt('user123'),
            'role' => 'user',
        ]);
    }
}
