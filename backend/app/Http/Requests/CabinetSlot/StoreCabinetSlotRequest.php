<?php

namespace App\Http\Requests\CabinetSlot;

use Illuminate\Foundation\Http\FormRequest;

class StoreCabinetSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_id'      => ['required', 'string', 'max:50', 'unique:cabinet_slots,slot_id'],
            'row_index'    => ['required', 'integer', 'min:0'],
            'column_index' => ['required', 'integer', 'min:0'],
        ];
    }
}
