<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'item_id'               => $this->item_id,
            'ticket_id'             => $this->ticket_id,
            'collateral_id'         => $this->collateral_id,
            'from_slot_id'          => $this->from_slot_id,
            'to_slot_id'            => $this->to_slot_id,
            'condition_description' => $this->condition_description,
            'image_url'             => $this->image_url,
            'verified_at'           => $this->verified_at,
            'collateral'            => new CollateralResource($this->whenLoaded('collateral')),
            'from_slot'             => new CabinetSlotResource($this->whenLoaded('fromSlot')),
            'to_slot'               => new CabinetSlotResource($this->whenLoaded('toSlot')),
        ];
    }
}
