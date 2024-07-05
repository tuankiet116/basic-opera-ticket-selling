<?php

namespace App\Services\Admin;

use App\Mail\PaymentSuccess;
use App\Models\BookModel;
use App\Models\ClientModel;
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
        $clients = ClientModel::whereRaw("LOWER(clients.phone_number) LIKE ?", ["%$searchString%"])
            ->orWhereRaw("LOWER(clients.banking_code) LIKE ?", ["%$searchString%"])
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
}
