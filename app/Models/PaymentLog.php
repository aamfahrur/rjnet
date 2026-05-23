<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    protected $table = 'payment_logs';

    protected $fillable = [
        'payment_id',
        'gateway',
        'event',
        'request_data',
        'response_data',
        'status',
        'error_message',
        'execution_time_ms',
        'ip_address',
    ];

    protected $casts = [
        'request_data'      => 'array',
        'response_data'     => 'array',
        'execution_time_ms' => 'float',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public static function log(
        string $gateway,
        string $event,
        ?array $request,
        ?array $response,
        string $status = 'success',
        ?string $error = null,
        ?float $executionTime = null,
        ?int $paymentId = null,
    ): self {
        return static::create([
            'payment_id'        => $paymentId,
            'gateway'           => $gateway,
            'event'             => $event,
            'request_data'      => $request,
            'response_data'     => $response,
            'status'            => $status,
            'error_message'     => $error,
            'execution_time_ms' => $executionTime,
            'ip_address'        => request()->ip(),
        ]);
    }
}
