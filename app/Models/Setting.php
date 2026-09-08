<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Auditable;
    protected $fillable = [
        'company_name',
        'company_logo',
        'phone',
        'email',
        'address',
        'timezone',
        'currency',
        'minimum_booking_amount',
        'maximum_booking_amount',
        'maximum_advance_booking_days',
        'cancellation_notice_hours',
        'welcome_credit',
        'welcome_credit_enabled',
        'referral_reward',
        'referral_reward_enabled',
        'google_review_reward',
        'google_review_enabled',
        'promotion_max_uses',
        'promotion_max_uses_per_customer',
    ];

    protected function casts(): array
    {
        return [
            'minimum_booking_amount' => 'decimal:2',
            'maximum_booking_amount' => 'decimal:2',
            'maximum_advance_booking_days' => 'integer',
            'cancellation_notice_hours' => 'integer',
            'welcome_credit' => 'decimal:2',
            'welcome_credit_enabled' => 'boolean',
            'referral_reward' => 'decimal:2',
            'referral_reward_enabled' => 'boolean',
            'google_review_reward' => 'decimal:2',
            'google_review_enabled' => 'boolean',
            'promotion_max_uses' => 'integer',
            'promotion_max_uses_per_customer' => 'integer',
        ];
    }

    public function getCompanyLogoUrlAttribute(): string
    {
        if (! empty($this->company_logo)) {
            if (filter_var($this->company_logo, FILTER_VALIDATE_URL)) {
                return $this->company_logo;
            }

            $path = ltrim($this->company_logo, '/');
            if (! str_starts_with($path, 'public/')) {
                $path = 'public/' . $path;
            }

            return asset($path);
        }

        return asset('public/assets/images/company_logo/brand_logo.png');
    }
}
