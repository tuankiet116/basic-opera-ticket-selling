<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

use function App\Helpers\trans_format;

class ApplyDiscountRequest extends FormRequest
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
            "token" => "required|string",
            "discount_code" => "required|string",
            "g-recaptcha-response" => "recaptcha"
        ];
    }

    public function attributes()
    {
        return [
            "discount_code" => trans_format("attributes.bookings.discount_code")
        ];
    }
}
