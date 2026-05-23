<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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
        $customer = auth()->user()->customer;
        $tickets = $customer?->tickets()->with('assignedTechnician')->latest()->paginate(10) ?? collect();
        return Inertia::render('Customer/Tickets/Index', ['tickets' => $tickets]);
    }

    public function create(): Response
    {
        return Inertia::render('Customer/Tickets/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = auth()->user()->customer;
        $ticket = $this->ticketService->openTicket($customer, $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'nullable|string',
        ]));

        return redirect()->route('customer.tickets.show', $ticket)
            ->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(int $id): Response
    {
        $ticket = auth()->user()->customer->tickets()->with(['replies.user', 'assignedTechnician'])->findOrFail($id);
        return Inertia::render('Customer/Tickets/Show', ['ticket' => $ticket]);
    }

    public function reply(Request $request, int $id): RedirectResponse
    {
        $ticket = auth()->user()->customer->tickets()->findOrFail($id);
        $this->ticketService->addReply($ticket, auth()->user(), $request->input('message'));
        return back()->with('success', 'Balasan dikirim.');
    }
}
