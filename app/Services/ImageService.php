<?php

namespace App\Services;

use App\Models\BookingImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Upload and optimize temporary booking images for Step 1 wizard submission using Intervention Image.
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
                    try {
                        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                        $filename = time() . '_' . uniqid() . '.' . $extension;
                        $fullPath = $destinationPath . '/' . $filename;

                        // Process and optimize image using Intervention Image facade
                        $image = Image::read($file);
                        $image->scaleDown(width: 1920, height: 1080);
                        $image->save($fullPath);

                        $uploadedImages[] = [
                            'path' => 'uploads/booking_images/temp/' . $filename,
                            'name' => $file->getClientOriginalName(),
                        ];
                    } catch (\Throwable $e) {
                        Log::error('Failed to process temporary booking image via Intervention Image: ' . $e->getMessage());

                        // Fallback: move file directly if Intervention Image fails
                        try {
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->move($destinationPath, $filename);
                            $uploadedImages[] = [
                                'path' => 'uploads/booking_images/temp/' . $filename,
                                'name' => $file->getClientOriginalName(),
                            ];
                        } catch (\Throwable $ex) {
                            Log::error('Fallback file upload failed: ' . $ex->getMessage());
                        }
                    }
                }
            }
        }

        if (empty($uploadedImages)) {
            return $existingImages;
        }

        return $uploadedImages;
    }

    /**
     * Move and process temporary images to permanent storage using Intervention Image and create BookingImage records.
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

            $fullTempPath = public_path($rawPath);

            if (!empty($rawPath) && file_exists($fullTempPath)) {
                $filename = basename($rawPath);
                $permPath = 'uploads/booking_images/' . $filename;
                $fullPermPath = public_path($permPath);

                try {
                    // Optimize and save permanent image using Intervention Image facade
                    $image = Image::read($fullTempPath);
                    $image->scaleDown(width: 1600, height: 1200);
                    $image->save($fullPermPath);

                    // Remove temporary file
                    if ($fullTempPath !== $fullPermPath && file_exists($fullTempPath)) {
                        @unlink($fullTempPath);
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to persist image via Intervention Image, falling back to rename: ' . $e->getMessage());
                    rename($fullTempPath, $fullPermPath);
                }

                BookingImage::create([
                    'booking_id' => $bookingId,
                    'image_path' => $permPath,
                    'image_name' => $rawName ?: $filename,
                ]);
            }
        }
    }
}
