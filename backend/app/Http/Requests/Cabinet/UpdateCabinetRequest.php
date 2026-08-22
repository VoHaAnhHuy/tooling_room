<?php

namespace App\Http\Requests\Cabinet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCabinetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cabinet_name'  => ['sometimes', 'string', 'max:100'],
            'total_rows'    => ['sometimes', 'integer', 'min:1'],
            'total_columns' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
