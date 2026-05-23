<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouterLog extends Model
{
    protected $fillable = [
        'router_id',
        'action',
        'command',
        'request_data',
        'response_data',
        'status',
        'error_message',
        'execution_time_ms',
    ];

    protected $casts = [
        'request_data'      => 'array',
        'response_data'     => 'array',
        'execution_time_ms' => 'float',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public static function logCommand(
        Router $router,
        string $action,
        ?string $command,
        ?array $request,
        ?array $response,
        string $status = 'success',
        ?string $error = null,
        ?float $executionTime = null,
    ): self {
        return static::create([
            'router_id'         => $router->id,
            'action'            => $action,
            'command'           => $command,
            'request_data'      => $request,
            'response_data'     => $response,
            'status'            => $status,
            'error_message'     => $error,
            'execution_time_ms' => $executionTime,
        ]);
    }
}
