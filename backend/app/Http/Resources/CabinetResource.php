<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CabinetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cabinet_id'     => $this->cabinet_id,
            'cabinet_name'   => $this->cabinet_name,
            'total_rows'     => $this->total_rows,
            'total_columns'  => $this->total_columns,
            'slots'          => CabinetSlotResource::collection($this->whenLoaded('slots')),
        ];
    }
}
