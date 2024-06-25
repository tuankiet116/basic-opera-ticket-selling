<?php

namespace App\Services\Admin;

use App\Models\BookModel;
use App\Models\DiscountModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountService
{
    public function create(array $data)
    {
        $discount = DiscountModel::create($data);
        return $discount->toArray();
    }

    public function updateDiscount(int $discountId, array $data)
    {
        DB::beginTransaction();
        try {
            $discount = DiscountModel::lockForUpdate()->find($discountId);
            if (!$discount) throw new Exception("Mã giảm giá không tồn tại");
            if ($data["quantity"] < $discount->quantity_used)
                throw new Exception("Số lượng áp dụng không thể ít hơn số lượng đã được sử dụng ($discount->quantity_used mã)");
            $discount->update($data);
            $discount->save();
            DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Cập nhật mã giảm giá lỗi: " . $e->getMessage());
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function deleteDiscount(DiscountModel $discount)
    {
        $bookings = BookModel::where("discount_code", $discount->discount_code)->first();
        if ($bookings) throw new \Exception("Mã giảm giá đã được sử dụng, không thể xóa");
        $discount->delete();
        return true;
    }

    public function listByEvent(int $eventId)
    {
        $discounts = DiscountModel::with("ticketClass")->where("event_id", $eventId)
            ->orderBy("created_at", "desc")->paginate(PAGINATE_NUMBER)->toArray();
        $books = BookModel::whereIn("discount_code", collect($discounts["data"])->pluck("discount_code"))->get();
        foreach ($discounts["data"] as &$discount) {
            $discount["deleteable"] = !collect($books)->where("discount_code", $discount["discount_code"])->count();
        }
        return $discounts;
    }
}
