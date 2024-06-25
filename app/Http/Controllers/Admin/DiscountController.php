<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Discount\CreateDiscountRequest;
use App\Http\Requests\Admin\Discount\UpdateDiscountRequest;
use App\Models\DiscountModel;
use App\Models\EventModel;
use App\Services\Admin\DiscountService;
use Exception;

class DiscountController extends Controller
{
    public function __construct(protected DiscountService $discountService)
    {
    }

    public function create(CreateDiscountRequest $request)
    {
        try {
            $discount = $this->discountService->create($request->validated());
            return $this->responseSuccess($discount);
        } catch (Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage()
            ]);
        }
    }

    public function index(EventModel $eventModel)
    {
        return $this->responseSuccess($this->discountService->listByEvent($eventModel->id));
    }

    public function update(int $discountId, UpdateDiscountRequest $request)
    {
        $data = $request->validated();
        try {
            $this->discountService->updateDiscount($discountId, $data);
            return $this->responseSuccess();
        } catch (Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage()
            ]);
        }
    }

    public function delete(DiscountModel $discountModel)
    {
        try {
            $this->discountService->deleteDiscount($discountModel);
            return $this->responseSuccess();
        } catch (Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage()
            ]);
        }
    }
}
