<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'payment_number',
        'invoice_id',
        'customer_id',
        'payment_gateway_id',
        'gateway',
        'gateway_transaction_id',
        'method',
        'channel',
        'amount',
        'admin_fee',
        'total_amount',
        'status',
        'va_number',
        'payment_url',
        'qr_url',
        'gateway_request',
        'gateway_response',
        'callback_data',
        'expired_at',
        'paid_at',
        'paid_by',
        'notes',
        'confirmed_by',
    ];

    protected $casts = [
        'amount'           => 'integer',
        'admin_fee'        => 'integer',
        'total_amount'     => 'integer',
        'gateway_request'  => 'array',
        'gateway_response' => 'array',
        'callback_data'    => 'array',
        'expired_at'       => 'datetime',
        'paid_at'          => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
            }
        });
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'payment_gateway_id');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast() && $this->status === 'pending';
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function markAsSuccess(array $callbackData, string $paidBy): void
    {
        $this->update([
            'status'        => 'success',
            'callback_data' => $callbackData,
            'paid_at'       => now(),
            'paid_by'       => $paidBy,
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'notes'  => $reason,
        ]);
    }
}
