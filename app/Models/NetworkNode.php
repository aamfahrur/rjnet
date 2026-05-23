<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NodeType;
use App\ValueObjects\GeoCoordinate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NetworkNode extends Model
{
    use SoftDeletes;

    protected $table = 'network_nodes';

    protected $fillable = [
        'name',
        'type',
        'status',
        'router_id',
        'parent_id',
        'ip_address',
        'mac_address',
        'port',
        'latitude',
        'longitude',
        'position',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'type'      => NodeType::class,
        'latitude'  => 'float',
        'longitude' => 'float',
        'position'  => 'array',
        'metadata'  => 'array',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NetworkNode::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NetworkNode::class, 'parent_id');
    }

    public function sourceLinks(): HasMany
    {
        return $this->hasMany(NetworkLink::class, 'source_node_id');
    }

    public function targetLinks(): HasMany
    {
        return $this->hasMany(NetworkLink::class, 'target_node_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(NetworkEvent::class, 'node_id');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function getCoordinate(): ?GeoCoordinate
    {
        if ($this->latitude && $this->longitude) {
            return new GeoCoordinate($this->latitude, $this->longitude);
        }
        return null;
    }

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    public function toReactFlowNode(): array
    {
        return [
            'id'       => (string) $this->id,
            'type'     => 'networkNode',
            'position' => $this->position ?? ['x' => 0, 'y' => 0],
            'data'     => [
                'label'     => $this->name,
                'nodeType'  => $this->type->value,
                'icon'      => $this->type->icon(),
                'status'    => $this->status,
                'ipAddress' => $this->ip_address,
                'port'      => $this->port,
            ],
        ];
    }
}
