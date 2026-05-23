<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrafficLog extends Model
{
    protected $table = 'traffic_logs';

    protected $fillable = [
        'router_id',
        'interface_name',
        'rx_bytes',
        'tx_bytes',
        'rx_packets',
        'tx_packets',
        'recorded_at',
    ];

    protected $casts = [
        'rx_bytes'    => 'integer',
        'tx_bytes'    => 'integer',
        'rx_packets'  => 'integer',
        'tx_packets'  => 'integer',
        'recorded_at' => 'datetime',
    ];

    public $timestamps = false;

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function getRxMbpsAttribute(): float
    {
        return round($this->rx_bytes * 8 / 1_000_000, 2);
    }

    public function getTxMbpsAttribute(): float
    {
        return round($this->tx_bytes * 8 / 1_000_000, 2);
    }
}
