<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateSpecialClientRequest extends FormRequest
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
            "name" => "required|string",
            "phone_number" => ["required", "string", "regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/"],
            "email" => "required|string|email",
            "address" => "required|string"
        ];
    }

    public function attributes()
    {
        return [
            "name" => "Tên",
            "phone_number" => "Số điện thoại",
            "email" => "Email",
            "address" => "Địa chỉ"
        ];
    }
}
