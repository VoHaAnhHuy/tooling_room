<?php

namespace App\Http\Requests\Collateral;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCollateralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id'         => ['sometimes', 'integer', 'exists:collateral_types,type_id'],
            'name'            => ['sometimes', 'string', 'max:150'],
            'location_status' => ['nullable', 'string', 'max:20'],
            'current_slot_id' => [
                'nullable', 'string', 'max:50', 'exists:cabinet_slots,slot_id',
                \Illuminate\Validation\Rule::unique('collaterals', 'current_slot_id')
                    ->ignore($this->route('collateral'), 'collateral_id')
            ],
            'status'          => ['nullable', 'string', 'max:20'],
        ];
    }
}
