<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CabinetSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slot_id'           => $this->slot_id,
            'cabinet_id'        => $this->cabinet_id,
            'row_index'         => $this->row_index,
            'column_index'      => $this->column_index,
            'cabinet'           => new CabinetResource($this->whenLoaded('cabinet')),
            'current_collateral'=> new CollateralResource($this->whenLoaded('currentCollateral')),
        ];
    }
}
