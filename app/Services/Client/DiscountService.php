<?php

namespace App\Services\Client;

use App\Models\BookModel;
use App\Models\DiscountModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function App\Helpers\trans_format;

class DiscountService
{
    const DISCOUNT_EXCEPTION = 10001;
    public function applyDiscount(string $discountCode, string $temporaryToken)
    {
        $discountInfo = null;
        DB::transaction(function () use ($discountCode, $temporaryToken, &$discountInfo) {
            $bookingEvent = BookModel::where("token", $temporaryToken)
                ->where("is_temporary", true)->first();
            $discount = DiscountModel::lockForUpdate()->where("discount_code", $discountCode)
                ->where("event_id", data_get($bookingEvent, "event_id"))
                ->where("start_date", "<=", Carbon::now()->format("Y-m-d"))
                ->where("end_date", ">=", Carbon::now()->format("Y-m-d"))->first();

            if (!$discount || !$bookingEvent) {
                if ($bookingEvent) $this->unApplyDiscountCodeFromBookings($temporaryToken, $bookingEvent->discount_code);
                Log::error("Mã giảm giá $discountCode không hợp lệ");
                DB::commit();
                throw new Exception(trans_format("messages.discount.invalid_code"), self::DISCOUNT_EXCEPTION);
            }

            $bookingsUsingThisDiscountCode = BookModel::where("token", $temporaryToken)
                ->where("discount_code", $discountCode)
                ->where("is_temporary", true)->count();
            $ticketClassId = $discount->ticket_class_id;
            $discountRemaining = $discount->quantity - $discount->quantity_used + $bookingsUsingThisDiscountCode;
            if ($discountRemaining == 0) {
                $this->unApplyDiscountCodeFromBookings($temporaryToken, $bookingEvent->discount_code);
                Log::info("Đã sử dụng hết mã giảm giá $discountCode cho hạng vé $ticketClassId");
                DB::commit();
                throw new Exception(trans_format("messages.discount.used_up"), self::DISCOUNT_EXCEPTION);
            }
            $bookingsUsingDiscount = BookModel::where("token", $temporaryToken)
                ->where("is_temporary", true)
                ->where("discount_code", "!=", null)
                ->update([
                    "discount_code" => null
                ]);

            $bookings = $bookingsValid = BookModel::where("token", $temporaryToken)
                ->where("discount_code", null)
                ->where("is_temporary", true)->get();

            $countBooking = collect($bookings)->count();
            if ($ticketClassId) {
                $bookingsValid = $bookings->where("ticket_class_id", $ticketClassId);
                $countBooking = collect($bookings)->where("ticket_class_id", $ticketClassId)->count();
            }
            if ($countBooking > $discountRemaining) {
                $bookingsValid->splice($discountRemaining);
                $countBooking = $discountRemaining;
            }

            $discountQuantityUsed = $discount->quantity_used;
            $discount->update([
                "quantity_used" => $discountQuantityUsed + $countBooking - $bookingsUsingDiscount,
            ]);
            $discount->save();
            $bookingsValid->each(function ($booking) use ($ticketClassId, $discount) {
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
            Log::info("Update discount ($discountCode - $discount->event_id) quantity from $discountQuantityUsed to $discount->quantity_used");
            $discountInfo = collect($discount)->except(["quantity", "quantity_used", "start_date", "end_date", "created_at", "updated_at"])->toArray();
            $seatsApplied = collect($bookingsValid)->pluck("seat_id");
            $discountInfo["applied"] = $seatsApplied;
        }, 5);
        return $discountInfo;
    }

    protected function unApplyDiscountCodeFromBookings($temporaryToken, $discountCodeUnApply)
    {
        try {
            $bookingHasDiscount = BookModel::where("token", $temporaryToken)
                ->where("is_temporary", true)
                ->where("discount_code", "=", $discountCodeUnApply)
                ->update([
                    "discount_code" => null
                ]);
            $discount = DiscountModel::lockForUpdate()->where("discount_code", $discountCodeUnApply)->first();
            if (!$discount) return;
            $discountQuantityUsed = $discount->quantity_used;
            $discount->update([
                "quantity_used" => $discountQuantityUsed - $bookingHasDiscount,
            ]);
            Log::info("Unapply discount ($discountCodeUnApply) quantity from $discountQuantityUsed to $discount->quantity_used");
        } catch (Exception $e) {
            Log::error("Error on unApplyDiscountCodeFromBookings: " . $e->getMessage());
            throw $e;
        }
    }
}
