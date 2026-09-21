<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case PENDING   = 'pending';
    case APPROVED  = 'approved';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING   => 'Pending',
            self::APPROVED  => 'Approved',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING   => 'badge-warning',
            self::APPROVED  => 'badge-success',
            self::CANCELLED => 'badge-danger',
        };
    }
}
