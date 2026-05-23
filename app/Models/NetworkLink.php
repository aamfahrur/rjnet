<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetworkLink extends Model
{
    protected $table = 'network_links';

    protected $fillable = [
        'source_node_id',
        'target_node_id',
        'type',
        'status',
        'media_type',
        'bandwidth_bps',
        'source_port',
        'target_port',
        'length_meters',
        'attenuation_db',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'bandwidth_bps'  => 'integer',
        'length_meters'  => 'float',
        'attenuation_db' => 'integer',
        'metadata'       => 'array',
    ];

    public function sourceNode(): BelongsTo
    {
        return $this->belongsTo(NetworkNode::class, 'source_node_id');
    }

    public function targetNode(): BelongsTo
    {
        return $this->belongsTo(NetworkNode::class, 'target_node_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(NetworkEvent::class, 'link_id');
    }

    public function toReactFlowEdge(): array
    {
        return [
            'id'       => "e{$this->id}",
            'source'   => (string) $this->source_node_id,
            'target'   => (string) $this->target_node_id,
            'type'     => 'smoothstep',
            'animated' => $this->status === 'active',
            'style'    => [
                'stroke'      => $this->status === 'active' ? '#22c55e' : '#ef4444',
                'strokeWidth' => 2,
            ],
            'data' => [
                'linkType'  => $this->type,
                'mediaType' => $this->media_type,
                'bandwidth' => $this->bandwidth_bps,
            ],
        ];
    }
}
