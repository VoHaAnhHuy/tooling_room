<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollateralTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type_id'   => $this->type_id,
            'type_name' => $this->type_name,
        ];
    }
}
