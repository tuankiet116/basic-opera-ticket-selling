<?php

namespace App\Http\Requests\Admin\Discount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
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
            "note" => "string|nullable",
            "quantity" => "required|integer",
            "start_date" => "nullable|date|date_format:Y-m-d|before_or_equal:end_date",
            "end_date" => "nullable|date|date_format:Y-m-d|after_or_equal:start_date",
        ];
    }

    public function attributes()
    {
        return [
            "event_id" => "Sự kiện áp dụng",
            "note" => "Ghi chú",
            "quantity" => "Số lượng áp dụng",
            "start_date" => "Ngày bắt đầu áp dụng",
            "end_date" => "Ngày kết thúc áp dụng",
        ];
    }
}
