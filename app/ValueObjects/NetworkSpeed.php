<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class NetworkSpeed
{
    private function __construct(
        private int $bitsPerSecond,
    ) {
    }

    public static function fromBps(int $bps): self
    {
        return new self($bps);
    }

    public static function fromKbps(float $kbps): self
    {
        return new self((int) round($kbps * 1000));
    }

    public static function fromMbps(float $mbps): self
    {
        return new self((int) round($mbps * 1_000_000));
    }

    public static function fromGbps(float $gbps): self
    {
        return new self((int) round($gbps * 1_000_000_000));
    }

    public function toBps(): int
    {
        return $this->bitsPerSecond;
    }

    public function toKbps(): float
    {
        return $this->bitsPerSecond / 1000;
    }

    public function toMbps(): float
    {
        return $this->bitsPerSecond / 1_000_000;
    }

    public function toGbps(): float
    {
        return $this->bitsPerSecond / 1_000_000_000;
    }

    public function toHuman(): string
    {
        return match (true) {
            $this->bitsPerSecond >= 1_000_000_000 => number_format($this->toGbps(), 2) . ' Gbps',
            $this->bitsPerSecond >= 1_000_000     => number_format($this->toMbps(), 2) . ' Mbps',
            $this->bitsPerSecond >= 1_000         => number_format($this->toKbps(), 2) . ' Kbps',
            default                               => $this->bitsPerSecond . ' bps',
        };
    }

    public function toMikrotikFormat(): string
    {
        return match (true) {
            $this->bitsPerSecond >= 1_000_000_000 => ((string) $this->toMbps()) . 'M',
            $this->bitsPerSecond >= 1_000_000     => ((string) $this->toMbps()) . 'M',
            $this->bitsPerSecond >= 1_000         => ((string) $this->toKbps()) . 'k',
            default                               => ((string) $this->bitsPerSecond),
        };
    }

    public function toBytesPerSecond(): int
    {
        return (int) ($this->bitsPerSecond / 8);
    }

    public function equals(self $other): bool
    {
        return $this->bitsPerSecond === $other->bitsPerSecond;
    }

    public function isFasterThan(self $other): bool
    {
        return $this->bitsPerSecond > $other->bitsPerSecond;
    }
}
