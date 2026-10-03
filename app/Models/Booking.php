<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id',
        'service_id',
        'answers',
        'frequency',
        'booking_date',
        'start_time',
        'end_time',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'unit_suite_floor',
        'suburb',
        'postcode',
        'special_instructions',
        'service_notes',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount_amount',
        'credit_used',
        'total_amount',
        'referal_code',
        'promo_code',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'status'  => BookingStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Booking $booking) {
            $statusVal = $booking->status instanceof BookingStatus ? $booking->status->value : strtolower((string) $booking->status);
            $paymentStatus = strtolower((string) $booking->payment_status);

            if ($statusVal === 'completed' && $paymentStatus === 'paid' && !empty($booking->referal_code)) {
                $referrer = User::where('referral_code', strtoupper($booking->referal_code))->first();
                if ($referrer && $referrer->id !== $booking->user_id) {
                    $alreadyRewarded = WalletTransaction::where('source', 'referral_bonus')
                        ->where('booking_id', $booking->id)
                        ->exists();

                    if (!$alreadyRewarded) {
                        $setting = Setting::first();
                        $rewardAmount = (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 25.00);

                        WalletTransaction::create([
                            'user_id'     => $referrer->id,
                            'booking_id'  => $booking->id,
                            'type'        => 'credit',
                            'amount'      => $rewardAmount,
                            'source'      => 'referral_bonus',
                            'description' => 'Referral bonus reward for completed Booking #BK-' . sprintf('%03d', $booking->id),
                        ]);
                    }
                }
            }
        });
    }
}
