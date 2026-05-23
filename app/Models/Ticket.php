<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'category',
        'status',
        'priority',
        'sla_deadline',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'closed_by',
        'rating',
        'resolution',
    ];

    protected $casts = [
        'status'            => TicketStatus::class,
        'priority'          => TicketPriority::class,
        'sla_deadline'      => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at'       => 'datetime',
        'closed_at'         => 'datetime',
        'rating'            => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            }
            if ($ticket->sla_deadline === null && $ticket->priority) {
                $ticket->sla_deadline = now()->addHours($ticket->priority->slaHours());
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeOpen($query)
    {
        return $query->where('status', TicketStatus::OPEN->value);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', TicketStatus::IN_PROGRESS->value);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNotIn('status', [
            TicketStatus::RESOLVED->value,
            TicketStatus::CLOSED->value,
        ]);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function assignTo(User $technician): void
    {
        $this->update([
            'assigned_to' => $technician->id,
            'status'      => $this->status === TicketStatus::OPEN
                ? TicketStatus::IN_PROGRESS
                : $this->status,
        ]);
    }

    public function resolve(string $resolution): void
    {
        $this->update([
            'status'      => TicketStatus::RESOLVED,
            'resolved_at' => now(),
            'resolution'  => $resolution,
        ]);
    }

    public function close(User $closedBy): void
    {
        $this->update([
            'status'    => TicketStatus::CLOSED,
            'closed_at' => now(),
            'closed_by' => $closedBy->id,
        ]);
    }

    public function recordFirstResponse(): void
    {
        if ($this->first_response_at === null) {
            $this->updateQuietly(['first_response_at' => now()]);
        }
    }

    public function isSlaBreached(): bool
    {
        return $this->sla_deadline && $this->sla_deadline->isPast()
            && !in_array($this->status, [TicketStatus::RESOLVED, TicketStatus::CLOSED]);
    }
}
