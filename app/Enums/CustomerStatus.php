<?php

declare(strict_types=1);

namespace App\Enums;

enum CustomerStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case ISOLATED = 'isolated';
    case TERMINATED = 'terminated';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE     => 'Aktif',
            self::SUSPENDED  => 'Suspend',
            self::ISOLATED   => 'Isolir',
            self::TERMINATED => 'Putus',
            self::PENDING    => 'Pending',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE     => 'green',
            self::SUSPENDED  => 'orange',
            self::ISOLATED   => 'red',
            self::TERMINATED => 'gray',
            self::PENDING    => 'blue',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE     => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::SUSPENDED  => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
            self::ISOLATED   => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::TERMINATED => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            self::PENDING    => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        };
    }
}
