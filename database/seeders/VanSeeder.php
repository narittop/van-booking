<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Van;

class VanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mockup data for vans
        $vans = [
            [
                'name' => 'Van 01',
                'license_plate' => '11-1111',
                'campus' => 'huntra',
                'owner_department' => 'gad',
                'capacity' => 13,
                'status' => 'active',
                'description' => 'Toyota Commuter (White)',
            ],
            [
                'name' => 'Van 02',
                'license_plate' => '22-2222',
                'campus' => 'wasukri',
                'owner_department' => 'subwa',
                'capacity' => 10,
                'status' => 'active',
                'description' => 'Toyota Ventury (Black)',
            ],
            [
                'name' => 'Van 03',
                'license_plate' => '33-3333',
                'campus' => 'nonthaburi',
                'owner_department' => 'subnon',
                'capacity' => 13,
                'status' => 'maintenance',
                'description' => 'Toyota Commuter (Silver)',
            ],
            [
                'name' => 'Van 04',
                'license_plate' => '44-4444',
                'campus' => 'huntra',
                'owner_department' => 'gad',
                'capacity' => 13,
                'status' => 'active',
                'description' => 'Hyundai H1 (Grey)',
            ],
            [
                'name' => 'Van 05',
                'license_plate' => '55-5555',
                'campus' => 'suphanburi',
                'owner_department' => 'subsu',
                'capacity' => 10,
                'status' => 'active',
                'description' => 'Toyota Majesty (White)',
            ],
             [
                'name' => 'Van 06',
                'license_plate' => '66-6666',
                'campus' => 'huntra',
                'owner_department' => 'gad',
                'capacity' => 13,
                'status' => 'active',
                'description' => 'Nissan Urvan (Silver)',
            ],
        ];

        foreach ($vans as $van) {
            Van::updateOrCreate(
                ['license_plate' => $van['license_plate']], // Use license plate to check for duplicates
                $van
            );
        }
    }
}
