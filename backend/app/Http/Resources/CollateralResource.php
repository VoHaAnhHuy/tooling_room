<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollateralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'collateral_id'   => $this->collateral_id,
            'name'            => $this->name,
            'status'          => $this->status,
            'location_status' => $this->location_status,
            'current_slot_id' => $this->current_slot_id,
            'type'            => new CollateralTypeResource($this->whenLoaded('type')),
            'current_slot'    => new CabinetSlotResource($this->whenLoaded('currentSlot')),
            // BelongsToMany cần dùng when() thay vì whenLoaded() trực tiếp
            'products'        => $this->when(
                $this->relationLoaded('products'),
                fn() => ProductResource::collection($this->products)
            ),
        ];
    }
}

