<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'referrer_user_id',
        'referral_code',
        'recipient_email',
        'referred_user_id',
        'booking_id',
        'status',
        'custom_message',
        'reward_amount',
    ];

    protected function casts(): array
    {
        return [
            'reward_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the user who sent the referral.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    /**
     * Get the user who registered using the referral.
     */
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Get the booking associated with the completed referral.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
