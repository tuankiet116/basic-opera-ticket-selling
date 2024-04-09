<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            "image" => "required|file|mimetypes:image/*|max:10240",
            "ticketClasses" => "array",
            "ticketClasses.*.name" => "required|string",
            "ticketClasses.*.price" => "required|numeric",
            "ticketClasses.*.color" => "required|string"
        ];
    }

    public function attributes()
    {
        return [
            "name" => __("attibutes.event-basic.name"),
            "date" => __("attibutes.event-basic.date"),
            "description" => __("attibutes.event-basic.desc"),
            "image" => __("attibutes.event-basic.image"),
            "ticketClasses.*.name" => __("attibutes.ticket-class.name"),
            "ticketClasses.*.price" => __("attibutes.ticket-class.price"),
            "ticketClasses.*.color" => __("attibutes.ticket-class.color"),
        ];
    }
}
