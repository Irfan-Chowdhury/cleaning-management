<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Services\ServiceManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ServiceManagementService();
    }

    public function test_it_sanitizes_whats_included_array()
    {
        $input = [
            '  Dusting surfaces  ',
            '',
            'Mopping floors',
            null,
            '   ',
            'Trash removal',
        ];

        $sanitized = $this->service->sanitizeWhatsIncluded($input);

        $this->assertEquals([
            'Dusting surfaces',
            'Mopping floors',
            'Trash removal',
        ], $sanitized);
    }

    public function test_it_creates_service_with_whats_included_json()
    {
        $service = $this->service->createService([
            'name' => 'Deep Cleaning',
            'description' => 'Comprehensive deep clean',
            'status' => 'active',
            'whats_included' => [
                'Deep oven clean',
                'Interior window wiping',
            ],
        ]);

        $this->assertInstanceOf(Service::class, $service);
        $this->assertEquals('Deep Cleaning', $service->name);
        $this->assertIsArray($service->whats_included);
        $this->assertCount(2, $service->whats_included);
        $this->assertEquals('Deep oven clean', $service->whats_included[0]);
    }

    public function test_it_updates_service_whats_included_array()
    {
        $service = Service::create([
            'name' => 'Standard Clean',
            'description' => 'Standard maintenance clean',
            'status' => 'active',
            'whats_included' => ['Vacuuming'],
        ]);

        $updatedService = $this->service->updateService($service, [
            'name' => 'Standard Clean Updated',
            'description' => 'Standard maintenance clean',
            'status' => 'active',
            'whats_included' => [
                'Vacuuming carpets',
                'Wiping countertops',
            ],
        ]);

        $this->assertEquals('Standard Clean Updated', $updatedService->name);
        $this->assertCount(2, $updatedService->whats_included);
        $this->assertEquals('Vacuuming carpets', $updatedService->whats_included[0]);
    }
}
