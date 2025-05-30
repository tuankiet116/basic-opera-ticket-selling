<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => "required|email",
            "password" => "required|min:8",
            "remember_me" => "boolean"
        ];
    }

    public function attributes()
    {
        return [
            "email" => "Email",
            "password" => "Mật khẩu",
            "remember_me" => "Ghi nhớ đăng nhập"
        ];
    }
}
