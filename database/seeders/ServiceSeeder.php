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
        $services = [
            // [
            //     'id' => 1,
            //     'name' => 'Commercial Cleaning',
            //     'description' => 'Standard recurring home cleaning',
            //     'whats_included' => json_encode([
            //         "Dusting & surface cleaning",
            //         "Vacuuming & mopping floors",
            //         "Restroom cleaning & sanitising",
            //         "Restroom cleaning & sanitising",
            //         "Bin emptying",
            //         "High-touch point disinfection",
            //     ]),
            //     'status' => 'active',
            // ],
            // ['id' => 2, 'name' => 'Domestic Cleaning', 'description' => 'Detailed top-to-bottom cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 3, 'name' => 'Window Cleaning', 'description' => 'Full move-out cleaning service', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 4, 'name' => 'Office & Bank Cleaning', 'description' => 'Commercial office cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 5, 'name' => 'Airbnb Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 6, 'name' => 'Healthcare Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 7, 'name' => 'School & College Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 8, 'name' => 'Strata Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 9, 'name' => 'End of Lease Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 10, 'name' => 'High Pressure Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            // ['id' => 11, 'name' => 'Carpet & Steam Cleaning', 'description' => 'Interior and exterior window cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 12, 'name' => 'NDIS & Aged Care Cleaning', 'description' => 'Specialised NDIS and aged care cleaning services', 'whats_included' => null, 'status' => 'active'],
            ['id' => 13, 'name' => 'Gym Cleaning', 'description' => 'Professional fitness centre and gym cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 14, 'name' => 'Childcare Cleaning', 'description' => 'Safe and hygienic childcare centre cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 15, 'name' => 'Bathroom Cleaning', 'description' => 'Thorough sanitising and deep bathroom cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 16, 'name' => 'Sports Centre Cleaning', 'description' => 'Comprehensive sports centre and facility cleaning', 'whats_included' => null, 'status' => 'active'],
            ['id' => 17, 'name' => 'Commercial Kitchen Cleaning', 'description' => 'Deep cleaning and degreasing for commercial kitchens', 'whats_included' => null, 'status' => 'active'],
            ['id' => 18, 'name' => 'Retail Cleaning', 'description' => 'Retail store and showroom cleaning services', 'whats_included' => null, 'status' => 'active'],
            ['id' => 19, 'name' => 'House Cleaning', 'description' => 'Reliable residential home cleaning services', 'whats_included' => null, 'status' => 'active'],
            ['id' => 20, 'name' => 'Deep Cleaning', 'description' => 'Intensive top-to-bottom deep cleaning service', 'whats_included' => null, 'status' => 'active'],
        ];

        // DB::table('services')->delete();
        // DB::statement('ALTER TABLE services AUTO_INCREMENT = 1');

        DB::table('services')->insert($services);
    }
}
