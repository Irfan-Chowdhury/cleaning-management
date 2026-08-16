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

        $services = [
            ['id' => 1, 'name' => 'Commercial Cleaning', 'description' => 'Standard recurring home cleaning', 'status' => 'active'],
            ['id' => 2, 'name' => 'Domestic Cleaning', 'description' => 'Detailed top-to-bottom cleaning', 'status' => 'active'],
            ['id' => 3, 'name' => 'Window Cleaning', 'description' => 'Full move-out cleaning service', 'status' => 'active'],
            ['id' => 4, 'name' => 'Office & Bank Cleaning', 'description' => 'Commercial office cleaning', 'status' => 'active'],
            ['id' => 5, 'name' => 'Airbnb Cleaning', 'description' => 'Interior and exterior window cleaning', 'status' => 'inactive'],
            ['id' => 6, 'name' => 'Healthcare Cleaning', 'description' => 'Interior and exterior window cleaning', 'status' => 'inactive'],
            ['id' => 7, 'name' => 'School & College Cleaning', 'description' => 'Interior and exterior window cleaning', 'status' => 'inactive'],
            ['id' => 8, 'name' => 'Strata Cleaning', 'description' => 'Interior and exterior window cleaning', 'status' => 'inactive'],
            ['id' => 9, 'name' => 'End of Lease Cleaning', 'description' => 'Interior and exterior window cleaning', 'status' => 'inactive'],
            ['id' => 10, 'name' => 'High Pressure Cleaning', 'description' => 'Interior and exterior window cleaning', 'status' => 'inactive'],
            ['id' => 11, 'name' => 'Carpet & Steam Cleaning', 'description' => 'Interior and exterior window cleaning', 'status' => 'inactive'],
        ];

        DB::table('services')->insert($services);
    }
}
