<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Regular Home Cleaning', 'description' => 'Standard recurring home cleaning', 'base_price' => 120.00, 'duration_minutes' => 120, 'status' => 'active'],
            ['name' => 'Deep Cleaning', 'description' => 'Detailed top-to-bottom cleaning', 'base_price' => 220.00, 'duration_minutes' => 240, 'status' => 'active'],
            ['name' => 'End of Lease Cleaning', 'description' => 'Full move-out cleaning service', 'base_price' => 320.00, 'duration_minutes' => 300, 'status' => 'active'],
            ['name' => 'Office Cleaning', 'description' => 'Commercial office cleaning', 'base_price' => 180.00, 'duration_minutes' => 180, 'status' => 'active'],
            ['name' => 'Window Cleaning', 'description' => 'Interior and exterior window cleaning', 'base_price' => 95.00, 'duration_minutes' => 90, 'status' => 'inactive'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
