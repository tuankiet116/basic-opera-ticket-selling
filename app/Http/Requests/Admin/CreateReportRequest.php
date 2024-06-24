<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
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
            "type" => "required|string|in:report-event,report-daily",
            "events" => "array|required",
            "events.*" => "required|integer",
            "start_date" => "required_if:type,report-daily|date|before_or_equal:end_date|date_format:Y-m-d|nullable",
            "end_date" => "required_if:type,report-daily|date|after_or_equal:start_date|date_format:Y-m-d|nullable"
        ];
    }

    public function attributes()
    {
        return [
            "type" => "Loại report",
            "events" => "Sự kiện",
            "start_date" => "Ngày bắt đầu",
            "end_date" => "Ngày kết thúc"
        ];
    }
}
