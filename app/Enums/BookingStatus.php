<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING    = 'pending';
    case APPROVED   = 'approved';
    case CONFIRMED  = 'confirmed';
    case PROCESSING = 'processing';
    case COMPLETED  = 'completed';
    case CANCELLED  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING    => 'Pending',
            self::APPROVED   => 'Approved',
            self::CONFIRMED  => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::COMPLETED  => 'Completed',
            self::CANCELLED  => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING    => 'badge-warning',
            self::APPROVED   => 'badge-info',
            self::CONFIRMED  => 'badge-primary',
            self::PROCESSING => 'badge-secondary',
            self::COMPLETED  => 'badge-success',
            self::CANCELLED  => 'badge-danger',
        };
    }
}
