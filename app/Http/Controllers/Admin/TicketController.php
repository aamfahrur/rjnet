<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Ticket\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $ticketService)
    {
    }

    public function index(): Response
    {
        $tickets = Ticket::with(['customer', 'assignedTechnician'])
            ->latest()
            ->paginate(25);
        return Inertia::render('Admin/Tickets/Index', ['tickets' => $tickets]);
    }

    public function show(Ticket $ticket): Response
    {
        $ticket->load(['customer', 'assignedTechnician', 'replies.user', 'attachments']);
        $technicians = User::role('teknisi')->get(['id', 'name']);
        return Inertia::render('Admin/Tickets/Show', ['ticket' => $ticket, 'technicians' => $technicians]);
    }

    public function reply(Ticket $ticket, Request $request): RedirectResponse
    {
        $this->ticketService->addReply($ticket, auth()->user(), $request->input('message'), $request->boolean('is_internal'));
        return back()->with('success', 'Balasan dikirim.');
    }

    public function assign(Ticket $ticket, Request $request): RedirectResponse
    {
        $technician = User::findOrFail($request->input('user_id'));
        $this->ticketService->assignTicket($ticket, $technician);
        return back()->with('success', 'Tiket di-assign.');
    }

    public function resolve(Ticket $ticket, Request $request): RedirectResponse
    {
        $this->ticketService->resolveTicket($ticket, $request->input('resolution'));
        return back()->with('success', 'Tiket di-resolve.');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $this->ticketService->closeTicket($ticket, auth()->user());
        return back()->with('success', 'Tiket ditutup.');
    }
}
