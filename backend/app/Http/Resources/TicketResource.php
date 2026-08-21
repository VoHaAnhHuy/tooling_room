<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ticket_id'         => $this->ticket_id,
            'ticket_type'       => $this->ticket_type,
            'related_ticket_id' => $this->related_ticket_id,
            'product_id'        => $this->product_id,
            'link_name'         => $this->link_name,
            'requester_name'    => $this->requester_name,
            'issuer_name'       => $this->issuer_name,
            'status'            => $this->status,
            'created_at'        => $this->created_at,
            'product'           => new ProductResource($this->whenLoaded('product')),
            'related_ticket'    => new TicketResource($this->whenLoaded('relatedTicket')),
            'child_tickets'     => TicketResource::collection($this->whenLoaded('childTickets')),
            'items'             => TicketItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
