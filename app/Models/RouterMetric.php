<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouterMetric extends Model
{
    protected $table = 'router_metrics';

    protected $fillable = [
        'router_id',
        'cpu_load',
        'memory_usage',
        'total_memory',
        'free_memory',
        'hdd_total',
        'hdd_free',
        'uptime_seconds',
        'active_connections',
        'pppoe_sessions',
        'hotspot_sessions',
        'dhcp_leases',
        'interface_stats',
        'recorded_at',
    ];

    protected $casts = [
        'cpu_load'           => 'float',
        'memory_usage'       => 'float',
        'total_memory'       => 'integer',
        'free_memory'        => 'integer',
        'hdd_total'          => 'integer',
        'hdd_free'           => 'integer',
        'uptime_seconds'     => 'float',
        'active_connections' => 'integer',
        'pppoe_sessions'     => 'integer',
        'hotspot_sessions'   => 'integer',
        'dhcp_leases'        => 'integer',
        'interface_stats'    => 'array',
        'recorded_at'        => 'datetime',
    ];

    public $timestamps = false;

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function getUptimeHumanAttribute(): string
    {
        $seconds = (int) $this->uptime_seconds;
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return trim("{$days}d {$hours}h {$minutes}m");
    }
}
