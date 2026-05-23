<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineSession extends Model
{
    protected $table = 'online_sessions';

    protected $fillable = [
        'router_id',
        'session_type',
        'username',
        'ip_address',
        'mac_address',
        'calling_id',
        'uptime_seconds',
        'bytes_in',
        'bytes_out',
        'connected_at',
        'recorded_at',
    ];

    protected $casts = [
        'uptime_seconds' => 'integer',
        'bytes_in'       => 'integer',
        'bytes_out'      => 'integer',
        'connected_at'   => 'datetime',
        'recorded_at'    => 'datetime',
    ];

    public $timestamps = false;

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }
}
