<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class GeoCoordinate
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        if ($latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException("Latitude must be between -90 and 90, got {$latitude}");
        }
        if ($longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException("Longitude must be between -180 and 180, got {$longitude}");
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (float) ($data['latitude'] ?? $data['lat'] ?? 0),
            (float) ($data['longitude'] ?? $data['lng'] ?? $data['lon'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'lat'       => $this->latitude,
            'lng'       => $this->longitude,
        ];
    }

    public function distanceInKm(self $other): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($other->latitude - $this->latitude);
        $dLon = deg2rad($other->longitude - $this->longitude);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($this->latitude)) * cos(deg2rad($other->latitude)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function toLeafletArray(): array
    {
        return [$this->latitude, $this->longitude];
    }

    public function toWKT(): string
    {
        return "POINT({$this->longitude} {$this->latitude})";
    }
}
