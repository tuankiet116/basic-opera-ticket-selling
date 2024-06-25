<?php

namespace App\Http\Requests\Admin\Discount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDiscountRequest extends FormRequest
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
            "discount_code" => [
                "required", "string", "max:20",
                Rule::unique("discounts", "discount_code")->where("event_id", $this->event_id)
            ],
            "ticket_class_id" => "nullable|integer|exists:ticket_classes,id",
            "discount_type" => "required|string|in:" . PRICE_DISCOUNT_TYPE . "," . PERCENTAGE_DISCOUNT_TYPE,
            "note" => "string|nullable",
            "quantity" => "required|integer",
            "price_discount" => "nullable|gt:0|numeric|required_if:discount_type," . PRICE_DISCOUNT_TYPE,
            "percentage_discount" => "nullable|gt:0|numeric|required_if:discount_type," . PERCENTAGE_DISCOUNT_TYPE,
            "start_date" => "nullable|date|date_format:Y-m-d|before_or_equal:end_date",
            "end_date" => "nullable|date|date_format:Y-m-d|after_or_equal:start_date",
        ];
    }

    public function attributes()
    {
        return [
            "event_id" => "Sự kiện áp dụng",
            "discount_code" => "Mã giảm giá",
            "ticket_class_id" => "Hạng vé áp dụng",
            "discount_type" => "Loại giảm giá",
            "note" => "Ghi chú",
            "quantity" => "Số lượng áp dụng",
            "price_discount" => "Số tiền giảm",
            "percentage_discount" => "Phần trăm giảm",
            "start_date" => "Ngày bắt đầu áp dụng",
            "end_date" => "Ngày kết thúc áp dụng",
        ];
    }
}
