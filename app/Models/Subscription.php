<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ConnectionType;
use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'package_id',
        'router_id',
        'connection_type',
        'status',
        'start_date',
        'end_date',
        'price_override',
        'billing_date',
        'auto_renewal',
        'notes',
    ];

    protected $casts = [
        'connection_type' => ConnectionType::class,
        'status'          => CustomerStatus::class,
        'start_date'      => 'date',
        'end_date'        => 'date',
        'billing_date'    => 'integer',
        'price_override'  => 'integer',
        'auto_renewal'    => 'boolean',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(InternetPackage::class, 'package_id');
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function pppoeAccount(): HasOne
    {
        return $this->hasOne(PPPoEAccount::class);
    }

    public function hotspotAccount(): HasOne
    {
        return $this->hasOne(HotspotAccount::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('status', CustomerStatus::ACTIVE->value);
    }

    public function scopeByConnectionType($query, ConnectionType $type)
    {
        return $query->where('connection_type', $type->value);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function getEffectivePrice(): int
    {
        return $this->price_override ?? $this->package->price;
    }

    public function getEffectivePriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->getEffectivePrice(), 0, ',', '.');
    }

    public function isActive(): bool
    {
        return $this->status === CustomerStatus::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === CustomerStatus::SUSPENDED;
    }

    public function suspend(): void
    {
        $this->update(['status' => CustomerStatus::SUSPENDED]);
    }

    public function unsuspend(): void
    {
        $this->update(['status' => CustomerStatus::ACTIVE]);
    }

    public function terminate(): void
    {
        $this->update([
            'status'   => CustomerStatus::TERMINATED,
            'end_date' => now(),
        ]);
    }
}
