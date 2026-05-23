<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingReminder extends Model
{
    protected $table = 'billing_reminders';

    protected $fillable = [
        'invoice_id',
        'customer_id',
        'days_before_due',
        'channel',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'days_before_due' => 'integer',
        'sent_at'         => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
