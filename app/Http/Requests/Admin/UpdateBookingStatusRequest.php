<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingStatusRequest extends FormRequest
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
            "client_id" => [
                "required",
                Rule::exists("clients", "id")->where("event_id", $this->get("event_id"))
            ],
            "event_id" => [
                "required",
                Rule::exists("events", "id")->where("is_delete", false)
            ]
        ];
    }

    public function attributes()
    {
        return [
            "client_id" => "Khách hàng",
            "event_id" => "Sự kiện"
        ];
    }
}
