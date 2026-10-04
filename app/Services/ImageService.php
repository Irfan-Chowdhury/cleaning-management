<?php

namespace App\Services;

use App\Models\BookingImage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Upload temporary booking images for Step 1 wizard submission.
     *
     * @param array|null $files Array of UploadedFile instances
     * @param array $existingImages Existing image records in session
     * @return array Array of image data containing ['path' => ..., 'name' => ...]
     */
    public function uploadTemporaryBookingImages(?array $files, array $existingImages = []): array
    {
        $uploadedImages = [];

        if (!empty($files) && is_array($files)) {
            $destinationPath = public_path('uploads/booking_images/temp');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            foreach ($files as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $filename);
                    $uploadedImages[] = [
                        'path' => 'uploads/booking_images/temp/' . $filename,
                        'name' => $file->getClientOriginalName(),
                    ];
                }
            }
        }

        if (empty($uploadedImages)) {
            return $existingImages;
        }

        return $uploadedImages;
    }

    /**
     * Move temporary images to permanent storage and create BookingImage records.
     *
     * @param int $bookingId
     * @param array $images
     * @return void
     */
    public function persistBookingImages(int $bookingId, array $images): void
    {
        if (empty($images) || !is_array($images)) {
            return;
        }

        $permDir = public_path('uploads/booking_images');
        if (!file_exists($permDir)) {
            mkdir($permDir, 0777, true);
        }

        foreach ($images as $imgData) {
            $rawPath = is_array($imgData) ? ($imgData['path'] ?? '') : (string) $imgData;
            $rawName = is_array($imgData) ? ($imgData['name'] ?? '') : basename($rawPath);

            if (!empty($rawPath) && file_exists(public_path($rawPath))) {
                $filename = basename($rawPath);
                $permPath = 'uploads/booking_images/' . $filename;
                rename(public_path($rawPath), public_path($permPath));

                BookingImage::create([
                    'booking_id' => $bookingId,
                    'image_path' => $permPath,
                    'image_name' => $rawName ?: $filename,
                ]);
            }
        }
    }
}
