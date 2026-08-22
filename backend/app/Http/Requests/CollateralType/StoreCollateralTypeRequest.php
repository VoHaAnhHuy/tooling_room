<?php

namespace App\Http\Requests\CollateralType;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollateralTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_name' => ['required', 'string', 'max:100', 'unique:collateral_types,type_name'],
        ];
    }
}
