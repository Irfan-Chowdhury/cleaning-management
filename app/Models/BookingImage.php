<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'image_path',
        'image_name',
    ];

    /**
     * Get the booking that owns the image.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
