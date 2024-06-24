<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            "date" => "required|date|date_format:Y-m-d",
            "description" => "required|string",
            "image" => "nullable|file|mimetypes:image/*|max:10240",
            "ticketClasses" => "array",
            "ticketClasses.*.id" => "nullable|integer",
            "ticketClasses.*.name" => "required|string",
            "ticketClasses.*.price" => "required|numeric",
            "ticketClasses.*.color" => "required|string",
            "banking_code" => "required|string|max:10"
        ];
    }

    public function attributes()
    {
        return [
            "name" => "Tên sự kiện",
            "date" => "Ngày công chiếu",
            "description" => "Mô tả",
            "image" => "Hình ảnh",
            "ticketClasses" => "Hạng vé",
            "ticketClasses.*.name" => "Tên hạng vé",
            "ticketClasses.*.price" => "Giá hạng vé",
            "ticketClasses.*.color" => "Màu sắc hạng vé",
            "banking_code" => "Mã chuyển khoản ngân hàng"
        ];
    }
}
