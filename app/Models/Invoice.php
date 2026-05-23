<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'subscription_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'issue_date',
        'due_date',
        'billing_period_start',
        'billing_period_end',
        'paid_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'subtotal'             => 'integer',
        'tax_amount'           => 'integer',
        'discount_amount'      => 'integer',
        'total_amount'         => 'integer',
        'paid_amount'          => 'integer',
        'remaining_amount'     => 'integer',
        'status'               => InvoiceStatus::class,
        'issue_date'           => 'date',
        'due_date'             => 'date',
        'billing_period_start' => 'date',
        'billing_period_end'   => 'date',
        'paid_at'              => 'datetime',
        'metadata'             => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
            if ($invoice->remaining_amount === null) {
                $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
            }
        });
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(BillingReminder::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopePending($query)
    {
        return $query->where('status', InvoiceStatus::PENDING->value);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::OVERDUE->value);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID->value);
    }

    public function scopeDueThisMonth($query)
    {
        return $query->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID;
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::OVERDUE;
    }

    public function markAsPaid(?string $paidAt = null): void
    {
        $this->update([
            'status'           => InvoiceStatus::PAID,
            'paid_amount'      => $this->total_amount,
            'remaining_amount' => 0,
            'paid_at'          => $paidAt ?? now(),
        ]);
    }

    public function markAsOverdue(): void
    {
        if ($this->status === InvoiceStatus::PENDING && $this->due_date->isPast()) {
            $this->update(['status' => InvoiceStatus::OVERDUE]);
        }
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getRemainingFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount, 0, ',', '.');
    }

    public function getPaymentProgressPercentAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 100;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 1);
    }

    public function getDaysUntilDueAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->due_date, false);
    }

    public function getIsPastDueAttribute(): bool
    {
        return $this->due_date->isPast() && $this->status !== InvoiceStatus::PAID;
    }
}
