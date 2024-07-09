<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class TemporaryBookingRequest extends FormRequest
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
                    return !!auth()->user() || $query->where("is_delete", false)->where("is_openning", true);
                })
            ],
            "bookings" => "array|required",
            "bookings.*.hall" => "required",
            "bookings.*.seats" => "array",
            "bookings.*.seats.*" => "string|required",
            "token" => "string|required",
            "is_booking" => "boolean",
            "g-recaptcha-response" => "required|recaptcha"
        ];
    }
}
