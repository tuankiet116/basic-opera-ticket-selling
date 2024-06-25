<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ApplyDiscountRequest;
use App\Services\Client\DiscountService;
use Exception;

class DiscountController extends Controller
{
    public function __construct(protected DiscountService $discountService)
    {
    }

    public function apply(ApplyDiscountRequest $request)
    {
        $discountCode = $request->get("discount_code");
        $temporaryToken = $request->get("token");
        try {
            $discount = $this->discountService->applyDiscount($discountCode, $temporaryToken);
            return $this->responseSuccess($discount ?? []);
        } catch (Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage(),
            ]);
        }
    }
}
