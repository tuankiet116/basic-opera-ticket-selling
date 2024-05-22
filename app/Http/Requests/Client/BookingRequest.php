<?php

namespace App\Http\Requests\Client;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use function App\Helpers\trans_format;

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
                    return $query->where("is_delete", false)->where("is_openning", true);
                })
            ],
            "name" => "required|string",
            "email" => "required|string|email",
            "phone_number" => ["required", "string", "regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/"],
            "is_receive_in_opera" => "required|boolean",
            "address" => "required_if:is_receive_in_opera,false",
            "id_number" => "required|numeric",
            "bookings" => "array|required",
            "bookings.*.hall" => "required",
            "bookings.*.seats" => "array",
            "bookings.*.seats.*" => "string|required",
            "g-recaptcha-response" => "recaptcha"
        ];
    }

    public function attributes()
    {
        return [
            "event_id" => trans_format("attributes.bookings.event"),
            "name" => trans_format("attributes.bookings.name"),
            "email" => trans_format("attributes.bookings.email"),
            "phone_number" => trans_format("attributes.bookings.phone_number"),
            "is_receive_in_opera" => trans_format("attributes.bookings.is_receive_in_opera"),
            "address" => trans_format("attributes.bookings.address"),
            "id_number" => trans_format("attributes.bookings.id_number"),
        ];
    }

    public function messages()
    {
        return [
            "address.required_if" => __("messages.booking.address_must_present")
        ];
    }
}
