<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RouterStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Router extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'router_group_id',
        'name',
        'host',
        'api_port',
        'api_ssl_port',
        'username',
        'password',
        'ssh_port',
        'snmp_community',
        'use_ssl',
        'is_active',
        'status',
        'latitude',
        'longitude',
        'capabilities',
        'router_os_version',
        'firmware_version',
        'board_name',
        'serial_number',
        'last_checked_at',
        'notes',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'api_port'        => 'integer',
        'api_ssl_port'    => 'integer',
        'ssh_port'        => 'integer',
        'use_ssl'         => 'boolean',
        'is_active'       => 'boolean',
        'latitude'        => 'float',
        'longitude'       => 'float',
        'capabilities'    => 'array',
        'last_checked_at' => 'datetime',
        'status'          => RouterStatus::class,
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function group(): BelongsTo
    {
        return $this->belongsTo(RouterGroup::class, 'router_group_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(RouterLog::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function pppoeAccounts(): HasMany
    {
        return $this->hasMany(PPPoEAccount::class);
    }

    public function hotspotAccounts(): HasMany
    {
        return $this->hasMany(HotspotAccount::class);
    }

    public function trafficLogs(): HasMany
    {
        return $this->hasMany(TrafficLog::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(RouterMetric::class);
    }

    public function onlineSessions(): HasMany
    {
        return $this->hasMany(OnlineSession::class);
    }

    public function networkNodes(): HasMany
    {
        return $this->hasMany(NetworkNode::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeOnline($query)
    {
        return $query->where('status', RouterStatus::ONLINE->value);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isOnline(): bool
    {
        return $this->status === RouterStatus::ONLINE;
    }

    public function getApiUrl(): string
    {
        $protocol = $this->use_ssl ? 'https' : 'http';
        $port = $this->use_ssl ? $this->api_ssl_port : $this->api_port;
        return "{$protocol}://{$this->host}:{$port}";
    }

    public function markOnline(): void
    {
        $this->updateQuietly([
            'status'          => RouterStatus::ONLINE,
            'last_checked_at' => now(),
        ]);
    }

    public function markOffline(): void
    {
        $this->updateQuietly([
            'status'          => RouterStatus::OFFLINE,
            'last_checked_at' => now(),
        ]);
    }
}
