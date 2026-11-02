<?php

namespace App\Services;

use App\Models\QuotationClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationClientService
{
    /**
     * Get base query for DataTable / listings.
     */
    public function query(): Builder
    {
        return QuotationClient::query()->select([
            'id',
            'contact_person',
            'company_name',
            'phone',
            'email',
            'address',
            'created_by',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * Get aggregate count of client profiles.
     */
    public function count(): int
    {
        return QuotationClient::count();
    }

    /**
     * Store a new client profile.
     */
    public function store(array $data): QuotationClient
    {
        return DB::transaction(function () use ($data) {
            $data = $this->normalizeData($data);
            $data['created_by'] = Auth::id();

            return QuotationClient::create($data);
        });
    }

    /**
     * Update an existing client profile.
     */
    public function update(QuotationClient $client, array $data): QuotationClient
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update($this->normalizeData($data));

            return $client->refresh();
        });
    }

    /**
     * Delete a client profile.
     */
    public function delete(QuotationClient $client): void
    {
        DB::transaction(function () use ($client) {
            $client->delete();
        });
    }

    /**
     * Search clients by phone, name, email, or company.
     * Used for live autocomplete on quotation forms.
     */
    public function search(string $term, int $limit = 10): Collection
    {
        $term = trim($term);
        $cleanPhone = preg_replace('/\s+/', '', $term);

        if ($term === '') {
            return new Collection();
        }

        return QuotationClient::query()
            ->where(function (Builder $query) use ($term, $cleanPhone) {
                if ($cleanPhone !== '') {
                    $query->where('phone', 'like', "%{$cleanPhone}%");
                }
                $query->orWhere('contact_person', 'like', "%{$term}%")
                    ->orWhere('company_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->orderBy('contact_person', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Normalize data attributes before persistence.
     */
    private function normalizeData(array $data): array
    {
        $normalized = Arr::only($data, [
            'contact_person',
            'company_name',
            'phone',
            'email',
            'address',
        ]);

        if (isset($normalized['phone'])) {
            $normalized['phone'] = preg_replace('/[^\d+]/', '', (string) $normalized['phone']);
        }

        if (isset($normalized['company_name']) && trim((string) $normalized['company_name']) === '') {
            $normalized['company_name'] = null;
        }

        return $normalized;
    }
}
