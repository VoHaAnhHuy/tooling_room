<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketItem\StoreTicketItemRequest;
use App\Http\Requests\TicketItem\UpdateTicketItemRequest;
use App\Http\Resources\TicketItemResource;
use App\Models\Ticket;
use App\Models\TicketItem;
use Illuminate\Http\JsonResponse;

class TicketItemController extends ApiController
{
    public function index(string $ticketId): JsonResponse
    {
        $ticket = Ticket::find($ticketId);
        if (!$ticket) return $this->notFound('Ticket not found');

        $items = TicketItem::with([
            'collateral.type',
            'fromSlot.cabinet',
            'toSlot.cabinet',
        ])->where('ticket_id', $ticketId)->get();

        return $this->success(TicketItemResource::collection($items));
    }

    public function store(StoreTicketItemRequest $request, string $ticketId): JsonResponse
    {
        $ticket = Ticket::find($ticketId);
        if (!$ticket) return $this->notFound('Ticket not found');

        $item = TicketItem::create(array_merge($request->validated(), [
            'ticket_id' => $ticketId,
        ]));
        $item->load(['collateral.type', 'fromSlot', 'toSlot']);
        return $this->created(new TicketItemResource($item));
    }

    public function show(int $itemId): JsonResponse
    {
        $item = TicketItem::with([
            'ticket',
            'collateral.type',
            'fromSlot.cabinet',
            'toSlot.cabinet',
        ])->find($itemId);

        if (!$item) return $this->notFound('Ticket item not found');
        return $this->success(new TicketItemResource($item));
    }

    public function update(UpdateTicketItemRequest $request, int $itemId): JsonResponse
    {
        $item = TicketItem::find($itemId);
        if (!$item) return $this->notFound('Ticket item not found');
        $item->update($request->validated());
        $item->load(['collateral', 'fromSlot', 'toSlot']);
        return $this->success(new TicketItemResource($item));
    }

    public function destroy(int $itemId): JsonResponse
    {
        $item = TicketItem::find($itemId);
        if (!$item) return $this->notFound('Ticket item not found');
        $item->delete();
        return $this->noContent('Ticket item deleted');
    }
}
