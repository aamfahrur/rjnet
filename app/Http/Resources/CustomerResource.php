<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'customer_code' => $this->customer_code,
            'full_name'     => $this->full_name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'phone_alt'     => $this->phone_alt,
            'id_number'     => $this->id_number,
            'status'        => [
                'value'      => $this->status->value,
                'label'      => $this->status->label(),
                'color'      => $this->status->color(),
                'badgeClass' => $this->status->badgeClass(),
            ],
            'registration_date' => $this->registration_date?->format('Y-m-d'),
            'current_package'   => $this->whenLoaded('activeSubscription', function () {
                return $this->activeSubscription?->package?->name;
            }),
            'subscription' => $this->whenLoaded('activeSubscription', function () {
                if (!$this->activeSubscription) {
                    return null;
                }
                return [
                    'id'              => $this->activeSubscription->id,
                    'package'         => $this->activeSubscription->package?->name,
                    'router'          => $this->activeSubscription->router?->name,
                    'connection_type' => $this->activeSubscription->connection_type?->value,
                    'start_date'      => $this->activeSubscription->start_date?->format('Y-m-d'),
                    'billing_date'    => $this->activeSubscription->billing_date,
                ];
            }),
            'primary_address' => $this->whenLoaded('primaryAddress', function () {
                return $this->primaryAddress ? [
                    'full'        => $this->primaryAddress->full_address,
                    'coordinates' => [
                        'lat' => $this->primaryAddress->latitude,
                        'lng' => $this->primaryAddress->longitude,
                    ],
                ] : null;
            }),
            'addresses' => $this->whenLoaded('addresses', function () {
                return $this->addresses->map(fn ($a) => [
                    'id'        => $a->id,
                    'label'     => $a->label,
                    'full'      => $a->full_address,
                    'latitude'  => $a->latitude,
                    'longitude' => $a->longitude,
                ]);
            }),
            'overdue_count' => $this->whenHas('overdue_count'),
            'created_at'    => $this->created_at?->format('Y-m-d H:i'),
            'updated_at'    => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }
}
