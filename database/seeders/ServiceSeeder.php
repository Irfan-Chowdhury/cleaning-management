<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{

    // php artisan db:seed --class=ServiceSeeder

    public function run(): void
    {
        DB::table('services')->delete();
        DB::statement('ALTER TABLE services AUTO_INCREMENT = 1');

        $services = [
            [
                'id' => 1,
                'name' => 'Commercial Cleaning',
                'description' => 'Standard recurring home cleaning',
                'whats_included' => json_encode([
                    "Dusting & surface cleaning",
                    "Vacuuming & mopping floors",
                    "Restroom cleaning & sanitising",
                    "Restroom cleaning & sanitising",
                    "Bin emptying",
                    "High-touch point disinfection",
                ]),
                'status' => 'active',
            ],
            ['id' => 2, 'name' => 'Domestic Cleaning', 'description' => 'Detailed top-to-bottom cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 3, 'name' => 'Window Cleaning', 'description' => 'Full move-out cleaning service', 'whats_included' => null, 'status' => 'active'],
            ['id' => 4, 'name' => 'Office & Bank Cleaning', 'description' => 'Commercial office cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 5, 'name' => 'Airbnb Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 6, 'name' => 'Healthcare Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 7, 'name' => 'School & College Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 8, 'name' => 'Strata Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 9, 'name' => 'End of Lease Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 10, 'name' => 'High Pressure Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 11, 'name' => 'Carpet & Steam Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
        ];

        DB::table('services')->insert($services);
    }
}
