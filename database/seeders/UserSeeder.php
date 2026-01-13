<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'ผู้ดูแลระบบ',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'ฝ่ายบริหาร',
            ]
        );

        // Create Sample User
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'ผู้ใช้ทดสอบ',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'ฝ่ายทรัพยากรบุคคล',
            ]
        );
    }
}
