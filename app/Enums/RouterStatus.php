<?php

declare(strict_types=1);

namespace App\Enums;

enum RouterStatus: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case MAINTENANCE = 'maintenance';
    case ERROR = 'error';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE      => 'Online',
            self::OFFLINE     => 'Offline',
            self::MAINTENANCE => 'Maintenance',
            self::ERROR       => 'Error',
        };
    }

    public function dotClass(): string
    {
        return match ($this) {
            self::ONLINE      => 'bg-green-500',
            self::OFFLINE     => 'bg-red-500',
            self::MAINTENANCE => 'bg-yellow-500',
            self::ERROR       => 'bg-orange-500',
        };
    }
}
