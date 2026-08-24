<?php

namespace App\Http\Requests\Collateral;

use Illuminate\Foundation\Http\FormRequest;

class AssignSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_id' => [
                'required', 
                'string', 
                'exists:cabinet_slots,slot_id',
                \Illuminate\Validation\Rule::unique('collaterals', 'current_slot_id')
                    ->ignore($this->route('collateral'), 'collateral_id')
            ],
        ];
    }
}
