<?php

namespace App\Enums;

enum PromotionStatus: int
{
    case INACTIVE = 0;
    case ACTIVE   = 1;
    case EXPIRED  = 2;

    public static function fromValue(mixed $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_numeric($value)) {
            return self::tryFrom((int) $value) ?? self::INACTIVE;
        }

        return match (strtolower((string) $value)) {
            'active', '1' => self::ACTIVE,
            'inactive', '0' => self::INACTIVE,
            'expired', '2' => self::EXPIRED,
            default => self::INACTIVE,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactive',
            self::ACTIVE   => 'Active',
            self::EXPIRED  => 'Expired',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::INACTIVE => 'badge-warning',
            self::ACTIVE   => 'badge-success',
            self::EXPIRED  => 'badge-secondary',
        };
    }
}
