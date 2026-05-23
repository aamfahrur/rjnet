<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially_paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING        => 'Belum Dibayar',
            self::PAID           => 'Lunas',
            self::PARTIALLY_PAID => 'Dibayar Sebagian',
            self::OVERDUE        => 'Jatuh Tempo',
            self::CANCELLED      => 'Dibatalkan',
            self::REFUNDED       => 'Dikembalikan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::PAID           => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::PARTIALLY_PAID => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            self::OVERDUE        => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::CANCELLED      => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            self::REFUNDED       => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::OVERDUE], true);
    }
}
