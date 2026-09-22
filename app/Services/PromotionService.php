<?php

namespace App\Services;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PromotionService
{
    /**
     * Return a query builder scoped for Yajra DataTables.
     */
    public function query(): Builder
    {
        return Promotion::query()
            ->with('creator:id,first_name,last_name')
            ->select([
                'id',
                'name',
                'code',
                'description',
                'discount_type',
                'discount_value',
                'status',
                'start_at',
                'expires_at',
                'new_customers_only',
                'existing_customers_only',
                'created_by',
                'created_at',
            ]);
    }

    public function store(array $data): Promotion
    {
        return Promotion::create($this->normalizeData($data) + [
            'created_by' => Auth::id(),
        ]);
    }

    public function update(Promotion $promotion, array $data): Promotion
    {
        $promotion->update($this->normalizeData($data));

        return $promotion->refresh();
    }

    public function delete(Promotion $promotion): void
    {
        $promotion->delete();
    }

    private function normalizeData(array $data): array
    {
        return [
            'name' => $data['name'],
            'code' => Str::upper($data['code']),
            'description' => $data['description'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'status' => (int) $data['status'],
            'start_at' => Carbon::parse($data['start_at'])->format('Y-m-d H:i:s'),
            'expires_at' => Carbon::parse($data['expires_at'])->format('Y-m-d H:i:s'),
            'new_customers_only' => (bool) ($data['new_customers_only'] ?? false),
            'existing_customers_only' => (bool) ($data['existing_customers_only'] ?? false),
        ];
    }
}
