<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
    case WHATSAPP = 'whatsapp';
    case SYSTEM = 'system';
    case PUSH = 'push';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL    => 'Email',
            self::TELEGRAM => 'Telegram',
            self::WHATSAPP => 'WhatsApp',
            self::SYSTEM   => 'System',
            self::PUSH     => 'Push Notification',
        };
    }
}
