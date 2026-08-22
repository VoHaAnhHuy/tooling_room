<?php

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;

class StoreCabinetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cabinet_id'    => ['required', 'string', 'max:50', 'unique:cabinets,cabinet_id'],
            'cabinet_name'  => ['required', 'string', 'max:100'],
            'total_rows'    => ['required', 'integer', 'min:1'],
            'total_columns' => ['required', 'integer', 'min:1'],
        ];
    }
}
