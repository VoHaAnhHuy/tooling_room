<?php

namespace App\Http\Requests\TicketItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'collateral_id'         => ['sometimes', 'string', 'max:50', 'exists:collaterals,collateral_id'],
            'from_slot_id'          => ['nullable', 'string', 'max:50', 'exists:cabinet_slots,slot_id'],
            'to_slot_id'            => ['nullable', 'string', 'max:50', 'exists:cabinet_slots,slot_id'],
            'condition_description' => ['nullable', 'string'],
            'image_url'             => ['nullable', 'url', 'max:300'],
            'verified_at'           => ['nullable', 'date'],
        ];
    }
}
