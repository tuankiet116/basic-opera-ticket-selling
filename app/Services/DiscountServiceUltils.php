<?php

namespace App\Services;

use App\Models\BookModel;
use App\Models\DiscountModel;
use Illuminate\Support\Facades\Log;

class DiscountServiceUltils
{
    public static function releaseDiscountInUsed(BookModel $bookModel)
    {
        $discount = DiscountModel::lockForUpdate()->where("discount_code", $bookModel->discount_code)->first();
        if (!$discount) return;
        $discountRemaining = $discount->quantity_used > 0 ? $discount->quantity_used - 1 : 0;
        Log::info("Release discount ($discount->discount_code - $discount->event_id) quantity from $discount->quantity_used to $discountRemaining");
        $discount->update([
            "quantity_used" => $discountRemaining
        ]);
        $discount->save();
    }
}
