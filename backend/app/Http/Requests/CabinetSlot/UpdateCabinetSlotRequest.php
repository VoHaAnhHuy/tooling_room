<?php

namespace App\Http\Requests\CabinetSlot;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCabinetSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'row_index'    => ['sometimes', 'integer', 'min:0'],
            'column_index' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
