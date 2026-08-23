<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerService
{
    public function query(): Builder
    {
        return User::query()
            ->where('role', 2)
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'gender',
                'photo',
                'is_active',
                'referral_code',
                'created_at',
            ]);
    }

    public function count(): int
    {
        return User::query()->where('role', 2)->count();
    }

    public function store(array $data): User
    {
        $data = $this->normalizeData($data);
        $data['role'] = 2;
        $data['created_by'] = Auth::id();

        $customer = User::create($data);

        if (empty($customer->referral_code)) {
            $customer->forceFill([
                'referral_code' => $this->generateReferralCode($customer),
            ])->save();
        }

        return $customer->refresh();
    }

    public function update(User $customer, array $data): User
    {
        abort_if((int) $customer->role !== 2, 404);

        $customer->update($this->normalizeData($data, true));

        return $customer->refresh();
    }

    public function delete(User $customer): void
    {
        abort_if((int) $customer->role !== 2, 404);

        $customer->delete();
    }

    private function normalizeData(array $data, bool $updating = false): array
    {
        $normalized = Arr::only($data, [
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
            'password',
        ]);

        $normalized['is_active'] = (bool) ($data['is_active'] ?? false);

        if ($updating && empty($normalized['password'])) {
            unset($normalized['password']);
        }

        return $normalized;
    }

    private function generateReferralCode(User $customer): string
    {
        $prefix = Str::upper(Str::limit(preg_replace('/[^A-Za-z]/', '', $customer->first_name), 4, ''));

        return ($prefix ?: 'CUST') . $customer->id;
    }
}
