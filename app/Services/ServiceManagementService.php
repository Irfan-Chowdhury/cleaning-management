<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceManagementService
{
    /**
     * Get all services ordered by latest created.
     */
    public function getAllServices(): Collection
    {
        return Service::latest()->get();
    }

    /**
     * Create a new service.
     */
    public function createService(array $data): Service
    {
        $data['whats_included'] = $this->sanitizeWhatsIncluded($data['whats_included'] ?? []);

        return Service::create($data);
    }

    /**
     * Update an existing service.
     */
    public function updateService(Service $service, array $data): Service
    {
        $data['whats_included'] = $this->sanitizeWhatsIncluded($data['whats_included'] ?? []);

        $service->update($data);

        return $service->fresh();
    }

    /**
     * Delete a service.
     */
    public function deleteService(Service $service): bool
    {
        return $service->delete();
    }

    /**
     * Sanitize and clean whats_included array by trimming strings and removing empty values.
     */
    public function sanitizeWhatsIncluded(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', $items),
            fn ($value) => $value !== '' && $value !== null
        ));
    }
}
