<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class IPv4Address
{
    private string $address;

    public function __construct(string $ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new \InvalidArgumentException("Invalid IPv4 address: {$ip}");
        }
        $this->address = $ip;
    }

    public static function tryFrom(string $ip): ?self
    {
        try {
            return new self($ip);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    public function toString(): string
    {
        return $this->address;
    }

    public function isPrivate(): bool
    {
        return filter_var($this->address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false;
    }

    public function toLong(): int
    {
        return ip2long($this->address);
    }

    public static function fromLong(int $long): self
    {
        $ip = long2ip($long);
        if ($ip === false) {
            throw new \InvalidArgumentException("Cannot convert long {$long} to IP");
        }
        return new self($ip);
    }

    public function inSubnet(string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        $subnetLong = ip2long($subnet);
        $ipLong = $this->toLong();
        $maskLong = -1 << (32 - (int) $mask);
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    public function __toString(): string
    {
        return $this->address;
    }
}
