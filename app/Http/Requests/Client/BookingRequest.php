<?php

namespace App\Http\Requests\Client;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
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
            "event_id" => [
                "required",
                Rule::exists("events", "id")->where(function (Builder $query) {
                    return $query->where("is_openning", true);
                })
            ],
            "name" => "required|string",
            "email" => "required|string|email",
            "phone_number" => ["required", "string", "regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/"],
            "is_receive_in_opera" => "required|boolean",
            "address" => "required_if:is_receive_in_opera,false",
            "bookings" => "array|required",
            "bookings.*.hall" => "required",
            "bookings.*.seats" => "array",
            "bookings.*.seats.*" => "string|required",
            "g-recaptcha-response" => "recaptcha"
        ];
    }
}
