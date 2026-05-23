<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetworkEvent extends Model
{
    protected $table = 'network_events';

    protected $fillable = [
        'node_id',
        'link_id',
        'router_id',
        'event_type',
        'severity',
        'title',
        'description',
        'data',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'data'        => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(NetworkNode::class, 'node_id');
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(NetworkLink::class, 'link_id');
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function resolve(): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);
    }
}
