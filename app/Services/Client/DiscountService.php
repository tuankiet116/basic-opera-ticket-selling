<?php

namespace App\Services\Client;

use App\Models\BookModel;
use App\Models\DiscountModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountService
{
    public function applyDiscount(string $discountCode, string $temporaryToken)
    {
        DB::beginTransaction();
        // Validate discount code and check quantity remaining, ticket class, and booking temporary token.
        try {
            $discount = DiscountModel::where("discount_code", $discountCode)
                ->where("start_date", "<=", Carbon::now()->format("Y-m-d"))
                ->where("end_date", ">=", Carbon::now()->format("Y-m-d"))->lockForUpdate()->first();

            if (!$discount) {
                Log::error("Mã giảm giá $discountCode không hợp lệ");
                throw new Exception("Mã giảm giá không hợp lệ");
            }

            $bookingsUsingThisDiscountCode = BookModel::where("token", $temporaryToken)
                ->where("discount_code", $discountCode)
                ->where("is_temporary", true)->count();
            $ticketClassId = $discount->ticket_class_id;
            $discountRemaining = $discount->quantity - $discount->quantity_used + $bookingsUsingThisDiscountCode;
            if ($discountRemaining == 0) {
                Log::info("Đã sử dụng hết mã giảm giá $discountCode cho hạng vé $ticketClassId");
                throw new Exception("Mã giảm giá đã được sử dụng hết");
            }
            $bookingsUsingDiscount = BookModel::where("token", $temporaryToken)
                ->where("is_temporary", true)
                ->where("discount_code", "!=", null)
                ->update([
                    "discount_code" => null
                ]);

            $bookings = BookModel::where("token", $temporaryToken)
                ->where("discount_code", null)
                ->where("is_temporary", true)->get();
            if (!sizeof($bookings)) goto END;

            $countBooking = collect($bookings)->count();
            if ($ticketClassId) {
                $countBooking = collect($bookings)->where("ticket_class_id", $ticketClassId)->count();
            }
            if ($countBooking > $discountRemaining) {
                $bookings->splice($discountRemaining);
                $countBooking = $discountRemaining;
            }
            $discount->update([
                "quantity_used" => $discount->quantity_used + $countBooking - $bookingsUsingDiscount,
            ]);
            $discount->save();
            $bookings->each(function ($booking) use ($ticketClassId, $discount) {
                if (($ticketClassId && $booking->ticket_class_id === $ticketClassId) || !$ticketClassId) {
                    $priceDiscount = $discount->price_discount;
                    if ($discount->discount_type == PERCENTAGE_DISCOUNT_TYPE) {
                        $priceDiscount = $booking->pricing * $discount->percentage_discount / 100;
                    }
                    $booking->update([
                        "discount_code" => $discount->discount_code,
                        "discount_price" => round($priceDiscount)
                    ]);
                    $booking->save();
                }
            });
            DB::commit();

            END:
            $discountInfo = collect($discount)->except(["quantity", "quantity_used", "start_date", "end_date", "created_at", "updated_at"])->toArray();
            $seatsApplied = collect($bookings)->pluck("seat_id");
            $discountInfo["applied"] = $seatsApplied;
            return $discountInfo;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
