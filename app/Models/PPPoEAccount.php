<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PPPoEAccount extends Model
{
    protected $table = 'pppoe_accounts';

    protected $fillable = [
        'customer_id',
        'subscription_id',
        'router_id',
        'username',
        'password',
        'profile',
        'service',
        'local_address',
        'remote_address',
        'caller_id',
        'disabled',
        'comment',
        'last_synced_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'disabled'       => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('disabled', false);
    }

    public function scopeByRouter($query, int $routerId)
    {
        return $query->where('router_id', $routerId);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isEnabled(): bool
    {
        return !$this->disabled;
    }

    public function markAsSynced(): void
    {
        $this->updateQuietly(['last_synced_at' => now()]);
    }

    public function toMikrotikArray(): array
    {
        return [
            'name'           => $this->username,
            'password'       => $this->password,
            'service'        => $this->service ?? 'pppoe',
            'profile'        => $this->profile ?? 'default',
            'local-address'  => $this->local_address ?? '',
            'remote-address' => $this->remote_address ?? '',
            'caller-id'      => $this->caller_id ?? '',
            'disabled'       => $this->disabled ? 'yes' : 'no',
            'comment'        => $this->comment ?? "Customer: {$this->customer->full_name}",
        ];
    }
}
