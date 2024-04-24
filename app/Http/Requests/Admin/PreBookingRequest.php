<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PreBookingRequest extends FormRequest
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
            "event_id" => "required|exists:events,id",
            "client_id" => [
                "required",
                Rule::exists("clients", "id")->where("isSpecial", true)
            ],
            "seats" => "array",
            "seats.*.hall" => "required",
            "seats.*.names" => "array",
            "seats.*.names.*" => "string|required",
        ];
    }
}
