<?php

declare(strict_types=1);

namespace App\Enums;

enum NodeType: string
{
    case ROUTER = 'router';
    case SWITCH = 'switch';
    case OLT = 'olt';
    case ODP = 'odp';
    case ONU = 'onu';
    case POP = 'pop';
    case SERVER = 'server';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ROUTER   => 'Router',
            self::SWITCH   => 'Switch',
            self::OLT      => 'OLT',
            self::ODP      => 'ODP',
            self::ONU      => 'ONU',
            self::POP      => 'POP',
            self::SERVER   => 'Server',
            self::CUSTOMER => 'Pelanggan',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ROUTER   => 'router',
            self::SWITCH   => 'hub',
            self::OLT      => 'device_hub',
            self::ODP      => 'podcasts',
            self::ONU      => 'router',
            self::POP      => 'location_city',
            self::SERVER   => 'dns',
            self::CUSTOMER => 'person',
        };
    }
}
