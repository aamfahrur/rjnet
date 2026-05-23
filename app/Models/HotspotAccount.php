<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotspotAccount extends Model
{
    protected $table = 'hotspot_accounts';

    protected $fillable = [
        'customer_id',
        'subscription_id',
        'router_id',
        'username',
        'password',
        'profile',
        'server',
        'mac_address',
        'uptime_limit',
        'bytes_in',
        'bytes_out',
        'disabled',
        'comment',
        'last_synced_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'disabled'       => 'boolean',
        'bytes_in'       => 'integer',
        'bytes_out'      => 'integer',
        'last_synced_at' => 'datetime',
    ];

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

    public function scopeActive($query)
    {
        return $query->where('disabled', false);
    }

    public function isEnabled(): bool
    {
        return !$this->disabled;
    }

    public function getTotalUsageBytesAttribute(): int
    {
        return $this->bytes_in + $this->bytes_out;
    }
}
