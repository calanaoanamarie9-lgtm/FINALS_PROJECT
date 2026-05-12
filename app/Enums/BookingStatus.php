<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case PickedUp = 'picked_up';
    case Cleaning = 'cleaning';
    case Completed = 'completed';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::PickedUp => 'Picked Up',
            self::Cleaning => 'Cleaning',
            self::Completed => 'Completed',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    /** @return list<self> */
    public static function staffProgression(): array
    {
        return [
            self::Pending,
            self::PickedUp,
            self::Cleaning,
            self::Completed,
            self::Delivered,
        ];
    }
}
