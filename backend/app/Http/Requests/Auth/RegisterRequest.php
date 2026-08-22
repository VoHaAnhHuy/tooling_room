<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'        => 'Email này đã được đăng ký.',
            'password.confirmed'  => 'Mật khẩu xác nhận không khớp.',
            'password.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ];
    }
}
