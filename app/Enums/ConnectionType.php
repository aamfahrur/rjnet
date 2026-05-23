<?php

declare(strict_types=1);

namespace App\Enums;

enum ConnectionType: string
{
    case PPPOE = 'pppoe';
    case HOTSPOT = 'hotspot';
    case STATIC_IP = 'static_ip';
    case DHCP = 'dhcp';

    public function label(): string
    {
        return match ($this) {
            self::PPPOE     => 'PPPoE',
            self::HOTSPOT   => 'Hotspot',
            self::STATIC_IP => 'Static IP',
            self::DHCP      => 'DHCP',
        };
    }

    public function mikrotikService(): string
    {
        return match ($this) {
            self::PPPOE     => 'ppp',
            self::HOTSPOT   => 'hotspot',
            self::STATIC_IP => 'static',
            self::DHCP      => 'dhcp',
        };
    }
}
