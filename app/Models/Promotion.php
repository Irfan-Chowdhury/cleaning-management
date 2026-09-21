<?php

namespace App\Models;

use App\Enums\PromotionStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_type',
        'discount_value',
        'status',
        'start_at',
        'expires_at',
        'new_customers_only',
        'existing_customers_only',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'start_at' => 'datetime',
            'expires_at' => 'datetime',
            'new_customers_only' => 'boolean',
            'existing_customers_only' => 'boolean',
        ];
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                if ($value instanceof PromotionStatus) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return PromotionStatus::tryFrom((int) $value) ?? PromotionStatus::INACTIVE;
                }
                return match (strtolower((string) $value)) {
                    'active', '1' => PromotionStatus::ACTIVE,
                    'inactive', '0' => PromotionStatus::INACTIVE,
                    'expired', '2' => PromotionStatus::EXPIRED,
                    default => PromotionStatus::INACTIVE,
                };
            },
            set: function (mixed $value) {
                if ($value instanceof PromotionStatus) {
                    return $value->value;
                }
                if (is_numeric($value)) {
                    return (int) $value;
                }
                return match (strtolower((string) $value)) {
                    'active' => PromotionStatus::ACTIVE->value,
                    'inactive' => PromotionStatus::INACTIVE->value,
                    'expired' => PromotionStatus::EXPIRED->value,
                    default => PromotionStatus::INACTIVE->value,
                };
            }
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
