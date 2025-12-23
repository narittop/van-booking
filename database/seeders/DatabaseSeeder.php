<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Van;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'ผู้ดูแลระบบ',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'ฝ่ายบริหาร',
        ]);

        // Create Sample User
        User::create([
            'name' => 'ผู้ใช้ทดสอบ',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'department' => 'ฝ่ายทรัพยากรบุคคล',
        ]);

        // Create Sample Vans
        Van::create([
            'name' => 'รถตู้หมายเลข 1',
            'license_plate' => 'กก 1234 กทม',
            'capacity' => 12,
            'status' => 'active',
            'description' => 'Toyota Commuter สีขาว',
        ]);

        Van::create([
            'name' => 'รถตู้หมายเลข 2',
            'license_plate' => 'ขข 5678 กทม',
            'capacity' => 12,
            'status' => 'active',
            'description' => 'Toyota Commuter สีดำ',
        ]);

        Van::create([
            'name' => 'รถตู้หมายเลข 3',
            'license_plate' => 'คค 9012 กทม',
            'capacity' => 15,
            'status' => 'active',
            'description' => 'Hyundai H1 สีเงิน',
        ]);
    }
}

