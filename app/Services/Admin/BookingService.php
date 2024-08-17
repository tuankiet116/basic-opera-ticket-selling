<?php

namespace App\Services\Admin;

use App\Mail\PaymentSuccess;
use App\Models\BookModel;
use App\Models\ClientModel;
use App\Models\DiscountModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\TicketClassModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    public function getBookings(int $eventId)
    {
        $searchString = request()->get("search");
        $clients = ClientModel::whereRaw("(LOWER(clients.phone_number) LIKE ? OR LOWER(clients.banking_code) LIKE ?)", ["%$searchString%", "%$searchString%"])
            ->where("isSpecial", false)
            ->where("event_id", $eventId)
            ->orderBy("created_at", "DESC")
            ->paginate(PAGINATE_NUMBER)->toArray();
        
        foreach ($clients["data"] as &$client) {
            $bookings = BookModel::with(["seat"])
                ->where("client_id", $client["id"])
                ->where("event_id", $eventId)->get()->toArray();
            $ticketClass = TicketClassModel::where("event_id", $eventId)->get()->toArray();

            foreach ($bookings as &$booking) {
                $eventSeatTicketClass = EventSeatClassModel::where("event_id", $eventId)->where("seat_id", $booking["seat_id"])->first();
                if ($eventSeatTicketClass) {
                    $indexTicketClass = array_search($eventSeatTicketClass->ticket_class_id, array_column($ticketClass, 'id'));
                    $booking["ticket_class"] = data_get($ticketClass, $indexTicketClass, []);
                }
            }
            $client["bookings"] = $bookings;
        }

        return $clients;
    }

    public function acceptBooking($data)
    {
        $eventId = $data["event_id"];
        $clientId = $data["client_id"];

        $event = EventModel::find($eventId);
        $client = ClientModel::find($clientId);
        $bookings = BookModel::with("seat")->where("client_id", $clientId)
            ->where("event_id", $eventId)
            ->where("start_pending", "!=", null)
            ->get();

        DB::beginTransaction();
        try {
            $bookings->each(function ($booking) {
                $booking->isBooked = true;
                $booking->isPending = false;
                $booking->start_pending = null;
                $booking->save();
            });
            Mail::to($client)->queue(new PaymentSuccess($event, $client, $bookings));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Accept bookings failed with error messages (clientID $clientId): " . $e->getMessage(), [
                "client" => $client->toArray()
            ]);
            return false;
        }
        DB::commit();
        return true;
    }

    public function cancelBookings(int $eventId, int $clientId)
    {
        DB::enableQueryLog();
        $currentBookings = BookModel::where([
            "event_id" => $eventId,
            "client_id" => $clientId,
            "is_temporary" => false
        ])->select(["id", "discount_code", "event_id"])->get()->toArray();
        
        $discountCodes = array_reduce($currentBookings, function($previous, $current) {
            if (!$current["discount_code"] || empty($current["discount_code"])) return $previous;
            if (isset($previous[$current["discount_code"]])) {
                $previous[$current["discount_code"]]++;
            } else {
                $previous[$current["discount_code"]] = 1;
            }
            return $previous;
        }, []);
        
        DB::beginTransaction();
        try {
            foreach ($discountCodes as $discountCode => $quantity) 
            {
                $discount = DiscountModel::where(["discount_code" => $discountCode, "event_id" => $eventId])->lockForUpdate()->first();
                $oldQuantityUsed = $discount->quantity_used;
                $discount->update([
                    "quantity_used" => $oldQuantityUsed - $quantity,
                ]);
                $discount->save();
                Log::info("(Cancel booking)Release discount ($discountCode - $discount->event_id) quantity from $oldQuantityUsed to ". ($discount->quantity_used));
            }
            $client = ClientModel::where([
                "id" => $clientId,
                "event_id" => $eventId,
            ])->first();

            BookModel::where([
                "event_id" => $eventId,
                "client_id" => $clientId,
                "is_temporary" => false
            ])->delete();

            ClientModel::where([
                "id" => $clientId,
                "event_id" => $eventId,
            ])->delete();
            Log::info("(Cancel booking) Delete client (client_id $clientId) and bookings (event_id $eventId)", $client->toArray());
            DB::commit();
        } catch (Exception $e) {
            Log::error("Xảy ra lỗi trong quá trình hủy booking: " . $e->getMessage());
            DB::rollBack();
            return false;
        }
        return true;
    }
}
