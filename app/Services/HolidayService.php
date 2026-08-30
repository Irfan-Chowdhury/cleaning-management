<?php

namespace App\Services;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Builder;

class HolidayService
{
    /**
     * Return a query builder scoped for Yajra DataTables.
     */
    public function query(): Builder
    {
        return Holiday::query()->select([
            'id',
            'title',
            'description',
            'start_date',
            'end_date',
            'is_active',
            'created_at',
        ]);
    }

    /**
     * Persist a new holiday record.
     */
    public function store(array $data): Holiday
    {
        return Holiday::create($this->normalizeData($data));
    }

    /**
     * Update an existing holiday record.
     */
    public function update(Holiday $holiday, array $data): Holiday
    {
        $holiday->update($this->normalizeData($data));

        return $holiday->refresh();
    }

    /**
     * Delete a holiday record.
     */
    public function delete(Holiday $holiday): void
    {
        $holiday->delete();
    }

    /**
     * Normalize incoming request data to safe, typed values.
     */
    private function normalizeData(array $data): array
    {
        return [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date'],
            'is_active'   => (bool) ($data['is_active'] ?? false),
        ];
    }
}
