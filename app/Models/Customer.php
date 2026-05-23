<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_code',
        'user_id',
        'full_name',
        'email',
        'phone',
        'phone_alt',
        'id_number',
        'id_card_image',
        'house_photo',
        'status',
        'registration_date',
        'termination_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status'            => CustomerStatus::class,
        'registration_date' => 'date',
        'termination_date'  => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->customer_code)) {
                $lastId = static::withTrashed()->max('id') ?? 0;
                $customer->customer_code = 'CUS-' . str_pad((string) ($lastId + 1), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function primaryAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class)->where('is_primary', true);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->whereIn('status', [
            CustomerStatus::ACTIVE->value,
            CustomerStatus::SUSPENDED->value,
        ])->latest();
    }

    public function pppoeAccounts(): HasMany
    {
        return $this->hasMany(PPPoEAccount::class);
    }

    public function hotspotAccounts(): HasMany
    {
        return $this->hasMany(HotspotAccount::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('status', CustomerStatus::ACTIVE->value);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', CustomerStatus::SUSPENDED->value);
    }

    public function scopeByStatus($query, CustomerStatus $status)
    {
        return $query->where('status', $status->value);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isActive(): bool
    {
        return $this->status === CustomerStatus::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === CustomerStatus::SUSPENDED;
    }

    public function hasOverdueInvoices(): bool
    {
        return $this->invoices()->where('status', \App\Enums\InvoiceStatus::OVERDUE->value)->exists();
    }

    public function getLatestInvoiceAttribute(): ?Invoice
    {
        return $this->invoices()->latest('issue_date')->first();
    }

    public function getCurrentPackageNameAttribute(): string
    {
        return $this->activeSubscription?->package?->name ?? 'Tidak ada paket';
    }
}
