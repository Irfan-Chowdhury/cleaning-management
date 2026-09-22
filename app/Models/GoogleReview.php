<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleReview extends Model
{
    use HasFactory, Auditable;

    protected $table = 'google_reviews';

    protected $fillable = [
        'user_id',
        'status',
        'reward_amount',
    ];

    protected function casts(): array
    {
        return [
            'status'        => ReviewStatus::class,
            'reward_amount' => 'decimal:2',
            'user_id'       => 'integer',
        ];
    }

    /**
     * Get the user that requested the Google Review Reward.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
