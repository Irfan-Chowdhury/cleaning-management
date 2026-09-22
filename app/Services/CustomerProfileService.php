<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CustomerProfileService
{
    /**
     * Update customer profile data and process avatar photo.
     *
     * @param  User  $user
     * @param  array  $data
     * @param  UploadedFile|null  $photoFile
     * @return User
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $photoFile = null): User
    {
        if ($photoFile !== null) {
            $user->photo = $this->processAndStorePhoto($user, $photoFile);
        }

        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'] ?? null;
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->gender = $data['gender'] ?? null;
        $user->address = $data['address'] ?? null;

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return $user->fresh();
    }

    /**
     * Process avatar photo with Intervention Image and return public path.
     *
     * @param  User  $user
     * @param  UploadedFile  $file
     * @return string
     */
    public function processAndStorePhoto(User $user, UploadedFile $file): string
    {
        $destination = public_path('assets/images/user_photos');

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = 'user-' . $user->id . '-' . time() . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();

        // Process image using Intervention Image (v3 cover 300x300)
        $image = Image::read($file);
        $image->cover(300, 300);
        $image->save($destination . '/' . $filename);

        // Delete old photo file if it exists and is stored locally
        if (! empty($user->photo) && ! filter_var($user->photo, FILTER_VALIDATE_URL)) {
            $oldPath = public_path(Str::after($user->photo, 'public/'));
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        return 'public/assets/images/user_photos/' . $filename;
    }
}
