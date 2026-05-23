<?php

declare(strict_types=1);

namespace App\Services\Ticket;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\HasTransaction;
use App\Traits\Loggable;

class TicketService
{
    use HasTransaction;
    use Loggable;

    protected function logPrefix(): string
    {
        return 'TicketService';
    }

    /**
     * Open a new support ticket.
     */
    public function openTicket(
        Customer $customer,
        array $data,
        ?User $openedBy = null,
    ): Ticket {
        return $this->transactional(function () use ($customer, $data, $openedBy) {
            $ticket = Ticket::create([
                'customer_id' => $customer->id,
                'user_id'     => $openedBy?->id,
                'subject'     => $data['subject'],
                'description' => $data['description'],
                'category'    => $data['category'] ?? 'general',
                'priority'    => TicketPriority::from($data['priority'] ?? 'medium'),
                'status'      => TicketStatus::OPEN,
            ]);

            $this->logInfo('Ticket opened', [
                'ticket'   => $ticket->ticket_number,
                'customer' => $customer->full_name,
                'priority' => $ticket->priority->value,
            ]);

            return $ticket;
        });
    }

    /**
     * Assign ticket to technician.
     */
    public function assignTicket(Ticket $ticket, User $technician): void
    {
        $ticket->assignTo($technician);
        $this->logInfo('Ticket assigned', [
            'ticket'     => $ticket->ticket_number,
            'technician' => $technician->name,
        ]);
    }

    /**
     * Add reply to ticket.
     */
    public function addReply(Ticket $ticket, User $user, string $message, bool $isInternal = false, array $attachments = []): void
    {
        $reply = $ticket->replies()->create([
            'user_id'     => $user->id,
            'message'     => $message,
            'is_internal' => $isInternal,
            'attachments' => $attachments,
        ]);

        // Record first response for SLA tracking
        $ticket->recordFirstResponse();

        // Update status based on who replied
        if ($user->isCustomer()) {
            $ticket->update(['status' => TicketStatus::IN_PROGRESS]);
        } elseif ($ticket->status === TicketStatus::OPEN) {
            $ticket->update(['status' => TicketStatus::IN_PROGRESS]);
        }

        $this->logInfo('Ticket reply added', [
            'ticket' => $ticket->ticket_number,
            'user'   => $user->name,
        ]);
    }

    /**
     * Resolve a ticket.
     */
    public function resolveTicket(Ticket $ticket, string $resolution): void
    {
        $ticket->resolve($resolution);
        $this->logInfo('Ticket resolved', ['ticket' => $ticket->ticket_number]);
    }

    /**
     * Close a ticket.
     */
    public function closeTicket(Ticket $ticket, User $closedBy): void
    {
        $ticket->close($closedBy);
        $this->logInfo('Ticket closed', [
            'ticket'    => $ticket->ticket_number,
            'closed_by' => $closedBy->name,
        ]);
    }

    /**
     * Get SLA status summary.
     */
    public function getSlaSummary(): array
    {
        return [
            'total_open'        => Ticket::open()->count(),
            'total_in_progress' => Ticket::inProgress()->count(),
            'sla_breached'      => Ticket::unresolved()
                ->whereNotNull('sla_deadline')
                ->where('sla_deadline', '<', now())
                ->count(),
            'avg_response_time_minutes' => $this->calculateAvgResponseTime(),
        ];
    }

    private function calculateAvgResponseTime(): float
    {
        $tickets = Ticket::whereNotNull('first_response_at')
            ->whereNotNull('created_at')
            ->latest()
            ->limit(100)
            ->get();

        if ($tickets->isEmpty()) {
            return 0;
        }

        return round($tickets->avg(
            fn ($t) => $t->created_at->diffInMinutes($t->first_response_at)
        ), 1);
    }
}
