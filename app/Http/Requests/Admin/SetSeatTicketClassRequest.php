<?php

namespace App\Http\Requests\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetSeatTicketClassRequest extends FormRequest
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
            "ticket_class_id" => [
                "required",
                Rule::exists("ticket_classes", "id")->where(function (Builder $query) {
                    return $query->where("event_id", $this->get("event_id"));
                })
            ],
            "event_id" => "required|exists:events,id",
            "seats" => "required|array",
            "seats.*.hall" => "required|integer",
            "seats.*.names" => "array",
            "seats.*.names.*" => "string",
        ];
    }
}
