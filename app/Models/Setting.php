<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'company_logo',
        'welcome_credit',
        'referral_reward',
        'google_review_reward',
        'maximum_advance_booking_days',
        'cancellation_notice_hours',
    ];

    protected function casts(): array
    {
        return [
            'welcome_credit' => 'decimal:2',
            'referral_reward' => 'decimal:2',
            'google_review_reward' => 'decimal:2',
            'maximum_advance_booking_days' => 'integer',
            'cancellation_notice_hours' => 'integer',
        ];
    }

    public function getCompanyLogoUrlAttribute(): string
    {
        if (! empty($this->company_logo)) {
            return filter_var($this->company_logo, FILTER_VALIDATE_URL)
                ? $this->company_logo
                : asset($this->company_logo);
        }

        return asset('public/assets/images/company_logo/brand_logo.png');
    }
}
