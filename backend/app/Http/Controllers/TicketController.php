<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $query   = Ticket::with(['product', 'items.collateral']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('ticket_type')) {
            $query->where('ticket_type', $request->ticket_type);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return $this->paginated($paginator, TicketResource::collection($paginator));
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = Ticket::create(array_merge($request->validated(), [
            'created_at' => now(),
        ]));
        $ticket->load(['product']);
        return $this->created(new TicketResource($ticket));
    }

    public function show(string $id): JsonResponse
    {
        $ticket = Ticket::with([
            'product',
            'relatedTicket',
            'childTickets',
            'items.collateral.type',
            'items.fromSlot.cabinet',
            'items.toSlot.cabinet',
        ])->find($id);

        if (!$ticket) return $this->notFound('Ticket not found');
        return $this->success(new TicketResource($ticket));
    }

    public function update(UpdateTicketRequest $request, string $id): JsonResponse
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return $this->notFound('Ticket not found');
        $ticket->update($request->validated());
        return $this->success(new TicketResource($ticket));
    }

    public function destroy(string $id): JsonResponse
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return $this->notFound('Ticket not found');
        $ticket->delete();
        return $this->noContent('Ticket deleted');
    }
}
