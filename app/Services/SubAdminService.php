<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SubAdminService
{
    public function query(): Builder
    {
        return User::query()
            ->where('role', 1)
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'gender',
                'photo',
                'is_active',
                'created_at',
            ]);
    }

    public function count(): int
    {
        return User::query()->where('role', 1)->count();
    }

    public function store(array $data): User
    {
        $normalized = $this->normalizeData($data);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $normalized['photo'] = $this->uploadPhoto($data['photo']);
        }

        $normalized['role'] = 1;
        $normalized['created_by'] = Auth::id();

        return User::create($normalized);
    }

    public function update(User $subAdmin, array $data): User
    {
        abort_if((int) $subAdmin->role !== 1, 404);

        $normalized = $this->normalizeData($data, true);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $normalized['photo'] = $this->uploadPhoto($data['photo'], $subAdmin->photo);
        }

        $subAdmin->update($normalized);

        return $subAdmin->refresh();
    }

    public function delete(User $subAdmin): void
    {
        abort_if((int) $subAdmin->role !== 1, 404);

        $this->deleteOldPhoto($subAdmin->photo);
        $subAdmin->delete();
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

    private function uploadPhoto(UploadedFile $file, ?string $oldPhoto = null): string
    {
        $destination = public_path('assets/images/users');

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0777, true, true);
        }

        $filename = 'sub-admin-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        $this->deleteOldPhoto($oldPhoto);

        return 'public/assets/images/users/' . $filename;
    }

    private function deleteOldPhoto(?string $oldPhoto): void
    {
        if (empty($oldPhoto) || filter_var($oldPhoto, FILTER_VALIDATE_URL)) {
            return;
        }

        $path = public_path(Str::after($oldPhoto, 'public/'));

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
