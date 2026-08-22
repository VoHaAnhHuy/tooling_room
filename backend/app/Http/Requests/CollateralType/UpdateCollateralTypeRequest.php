<?php

namespace App\Http\Requests\CollateralType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCollateralTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy {id} từ route để bỏ qua chính nó khi kiểm tra unique
        $id = $this->route('id');

        return [
            'type_name' => ['required', 'string', 'max:100', "unique:collateral_types,type_name,{$id},type_id"],
        ];
    }
}
