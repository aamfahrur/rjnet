<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentGateway: string
{
    case IPAYMU = 'ipaymu';
    case DUITKU = 'duitku';
    case MIDTRANS = 'midtrans';

    public function label(): string
    {
        return match ($this) {
            self::IPAYMU   => 'iPaymu',
            self::DUITKU   => 'Duitku',
            self::MIDTRANS => 'Midtrans',
        };
    }

    public function driverClass(): string
    {
        return match ($this) {
            self::IPAYMU   => \App\Services\Payment\Drivers\IPaymuDriver::class,
            self::DUITKU   => \App\Services\Payment\Drivers\DuitkuDriver::class,
            self::MIDTRANS => \App\Services\Payment\Drivers\MidtransDriver::class,
        };
    }
}
