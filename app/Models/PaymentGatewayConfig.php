<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGatewayConfig extends Model
{
    protected $table = 'payment_gateways';

    protected $fillable = [
        'name',
        'code',
        'driver_class',
        'config',
        'logo',
        'supported_methods',
        'admin_fee_percentage',
        'admin_fee_fixed',
        'is_active',
        'is_sandbox',
        'sort_order',
    ];

    protected $casts = [
        'config'               => 'array',
        'supported_methods'    => 'array',
        'admin_fee_percentage' => 'float',
        'admin_fee_fixed'      => 'integer',
        'is_active'            => 'boolean',
        'is_sandbox'           => 'boolean',
        'sort_order'           => 'integer',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_gateway_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isIPaymu(): bool
    {
        return $this->code === 'ipaymu';
    }

    public function isDuitku(): bool
    {
        return $this->code === 'duitku';
    }

    public function isMidtrans(): bool
    {
        return $this->code === 'midtrans';
    }

    public function getApiKey(): ?string
    {
        return $this->config['api_key'] ?? null;
    }

    public function getCallbackUrl(): string
    {
        return route('api.payment.callback', ['gateway' => $this->code]);
    }

    public function calculateAdminFee(int $amount): int
    {
        $percentage = (int) round($amount * ($this->admin_fee_percentage / 100));
        return $percentage + $this->admin_fee_fixed;
    }
}
