<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketItemResource;
use App\Models\Ticket;
use App\Models\TicketItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request, string $ticketId): JsonResponse
    {
        $ticket = Ticket::find($ticketId);
        if (!$ticket) return $this->notFound('Ticket not found');

        $validated = $request->validate([
            'collateral_id'         => 'required|string|max:50|exists:collaterals,collateral_id',
            'from_slot_id'          => 'nullable|string|max:50|exists:cabinet_slots,slot_id',
            'to_slot_id'            => 'nullable|string|max:50|exists:cabinet_slots,slot_id',
            'condition_description' => 'nullable|string',
            'image_url'             => 'nullable|url|max:300',
            'verified_at'           => 'nullable|date',
        ]);
        $validated['ticket_id'] = $ticketId;
        $item = TicketItem::create($validated);
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

    public function update(Request $request, int $itemId): JsonResponse
    {
        $item = TicketItem::find($itemId);
        if (!$item) return $this->notFound('Ticket item not found');
        $validated = $request->validate([
            'collateral_id'         => 'sometimes|string|max:50|exists:collaterals,collateral_id',
            'from_slot_id'          => 'nullable|string|max:50|exists:cabinet_slots,slot_id',
            'to_slot_id'            => 'nullable|string|max:50|exists:cabinet_slots,slot_id',
            'condition_description' => 'nullable|string',
            'image_url'             => 'nullable|url|max:300',
            'verified_at'           => 'nullable|date',
        ]);
        $item->update($validated);
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
