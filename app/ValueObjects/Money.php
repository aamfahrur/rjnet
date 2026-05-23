<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Money
{
    private function __construct(
        private int $amountInCents,
        private string $currency = 'IDR',
    ) {
    }

    public static function fromCents(int $cents, string $currency = 'IDR'): self
    {
        return new self($cents, $currency);
    }

    public static function fromFloat(float $amount, string $currency = 'IDR'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public function toCents(): int
    {
        return $this->amountInCents;
    }

    public function toFloat(): float
    {
        return $this->amountInCents / 100;
    }

    public function toRupiah(): string
    {
        return 'Rp ' . number_format($this->toFloat(), 0, ',', '.');
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amountInCents + $other->amountInCents, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amountInCents - $other->amountInCents, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amountInCents * $factor, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amountInCents === $other->amountInCents
            && $this->currency === $other->currency;
    }

    public function isGreaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amountInCents > $other->amountInCents;
    }

    public function isZero(): bool
    {
        return $this->amountInCents === 0;
    }

    public function isNegative(): bool
    {
        return $this->amountInCents < 0;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException(
                "Currency mismatch: {$this->currency} vs {$other->currency}"
            );
        }
    }

    public function __toString(): string
    {
        return $this->toRupiah();
    }
}
