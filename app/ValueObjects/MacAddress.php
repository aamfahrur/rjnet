<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class MacAddress
{
    private string $normalized;

    public function __construct(string $mac)
    {
        $cleaned = strtoupper(preg_replace('/[^A-F0-9]/', '', strtoupper($mac)));
        if (strlen($cleaned) !== 12 || !ctype_xdigit($cleaned)) {
            throw new \InvalidArgumentException("Invalid MAC address: {$mac}");
        }
        $this->normalized = implode(':', str_split($cleaned, 2));
    }

    public static function tryFrom(string $mac): ?self
    {
        try {
            return new self($mac);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    public function toString(): string
    {
        return $this->normalized;
    }

    public function toColonFormat(): string
    {
        return $this->normalized;
    }

    public function toDashFormat(): string
    {
        return str_replace(':', '-', $this->normalized);
    }

    public function toNoSeparator(): string
    {
        return str_replace(':', '', $this->normalized);
    }

    public function toMikrotikFormat(): string
    {
        return $this->toColonFormat();
    }

    public function getOui(): string
    {
        return substr($this->normalized, 0, 8);
    }

    public function equals(self $other): bool
    {
        return $this->normalized === $other->normalized;
    }

    public function __toString(): string
    {
        return $this->normalized;
    }
}
