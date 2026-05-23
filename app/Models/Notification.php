<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'customer_id',
        'type',
        'channel',
        'title',
        'message',
        'data',
        'target_url',
        'is_read',
        'read_at',
        'is_sent',
        'sent_at',
        'sent_via',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'data'        => 'array',
        'is_read'     => 'boolean',
        'is_sent'     => 'boolean',
        'read_at'     => 'datetime',
        'sent_at'     => 'datetime',
        'retry_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsSent(string $via): void
    {
        $this->update([
            'is_sent'  => true,
            'sent_at'  => now(),
            'sent_via' => $via,
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->increment('retry_count');
        $this->update(['error_message' => $error]);
    }
}
