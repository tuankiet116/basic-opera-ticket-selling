<?php

namespace App\Services\Client;

use App\Events\AdminClientBookingTicket;
use App\Events\ClientBookingTicket;
use App\Events\ClientRemoveBookingTicket;
use App\Mail\AskingPayment;
use App\Models\BookModel;
use App\Models\ClientModel;
use App\Models\DiscountModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\SeatModel;
use App\Models\TicketClassModel;
use App\Services\DiscountServiceUltils;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function App\Helpers\generateRandomString;

class BookingService
{
    public function booking(array $data)
    {
        DB::beginTransaction();
        $temporaryToken = data_get($data, "token");
        try {
            $event = EventModel::find($data["event_id"]);
            $clientBookingCode = $event->banking_code . "-" . substr(time(), -2) . generateRandomString();
            $client = ClientModel::create([
                "name" => data_get($data, "name"),
                "email" => data_get($data, "email"),
                "phone_number" => data_get($data, "phone_number"),
                "is_receive_in_opera" => data_get($data, "is_receive_in_opera"),
                "address" => data_get($data, "address"),
                "event_id" => data_get($data, "event_id"),
                "id_number" => data_get($data, "id_number"),
                "isSpecial" => false,
                "banking_code" => $clientBookingCode
            ]);
            $bookings = data_get($data, "bookings");
            $seatsBooking = [];
            $dataBookingSendMail = [];
            $dataClient = $client->toArray();
            $dataBookings = [];
            BookModel::where("token", $temporaryToken)->update(["is_temporary" => false]);
            foreach ($bookings as $booking) {
                $seats = SeatModel::whereIn("name", data_get($booking, "seats"))->where("hall", data_get($booking, "hall"))->get();
                $dataBookingSendMail[$booking["hall"]] = [];
                if ($seats) $seats = $seats->toArray();
                else $seats = [];
                array_push($seatsBooking, ...$seats);
                foreach ($seats as $seat) {
                    $bookingClient = BookModel::where([
                        "token" => $temporaryToken,
                        "seat_id" => $seat["id"],
                        "event_id" => data_get($data, "event_id")
                    ])->first();
                    if ($bookingClient) {
                        $bookingClient->update([
                            "client_id" => $client->id,
                            "token" => null,
                            "start_pending" => Carbon::now()->format("Y-m-d H:i:s"),
                            "isPending" => true,
                        ]);
                        $bookingClient->save();
                        $ticketClass = TicketClassModel::find($bookingClient->ticket_class_id);
                    } else {
                        $ticketClass = EventSeatClassModel::with("ticketClass")->where([
                            "seat_id" => $seat["id"],
                            "event_id" => data_get($data, "event_id")
                        ])->first()->ticketClass;
                        $bookingClient = BookModel::create([
                            "client_id" => $client->id,
                            "token" => null,
                            "start_pending" => Carbon::now()->format("Y-m-d H:i:s"),
                            "isPending" => true,
                            "seat_id" => $seat["id"],
                            "event_id" => data_get($data, "event_id"),
                            "ticket_class_id" => $ticketClass->id,
                            "is_client_special" => false,
                            "is_temporary" => false,
                            "pricing" => $ticketClass->price,
                        ]);
                    }

                    $dataBookings[] = [
                        ...$bookingClient->toArray(),
                        "seat" => $seat,
                        "ticket_class" => $ticketClass->toArray()
                    ];
                    array_push($dataBookingSendMail[$booking["hall"]], [
                        "class" => $ticketClass->name,
                        "seat" => $seat["name"],
                        "price" => $bookingClient->pricing,
                        "discount_price" => $bookingClient->pricing - ($bookingClient->discount_price ?? 0)
                    ]);
                }
            }
            BookModel::where(["token" => $temporaryToken])->delete();

            Mail::to($client)->queue(new AskingPayment($event, $client, $dataBookingSendMail));

            $dataAdminBookingTicket = [...$dataClient];
            foreach (array_chunk($dataBookings, CHUNK_SIZE_BROADCAST) as $dataBookingsChunk) {
                $dataAdminBookingTicket["bookings"] = $dataBookingsChunk;
                AdminClientBookingTicket::dispatch($dataAdminBookingTicket, $event);
            }
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return __("messages.booking.is_booked");
            }
            Log::error("Error while customers booking tickets: " . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Booking Error: " . $e->getMessage(), $data);
            return __("messages.booking.error");
        } finally {
            DB::commit();
        }
        return true;
    }

    public function temporaryBooking(array $data)
    {
        $token = data_get($data, "token");
        $eventId = $data["event_id"];
        $startTime = Carbon::now()->format("Y-m-d H:i:s");
        $firstTemporary = BookModel::where("token", $token)->first();
        if ($firstTemporary) $startTime = $firstTemporary->created_at;
        foreach ($data["bookings"] as $dBooking) {
            $seats = SeatModel::whereIn("name", $dBooking["seats"])->where("hall", $dBooking["hall"])->get()->toArray();
            foreach ($seats as $seat) {
                $ticketClass = EventSeatClassModel::with(["ticketClass", "event"])->where("seat_id", data_get($seat, "id"))->where("event_id", $eventId)->first();
                if (!$ticketClass || !$ticketClass->ticketClass) throw new Exception("No ticket class found while booking.");
                if (data_get($data, "is_booking")) {
                    BookModel::insert([
                        "event_id" => $eventId,
                        "seat_id" => data_get($seat, "id"),
                        "ticket_class_id" => $ticketClass->ticketClass->id,
                        "isBooked" => false,
                        "isPending" => false,
                        "start_pending" => null,
                        "is_temporary" => true,
                        "token" => $token,
                        "pricing" => $ticketClass->ticketClass->price,
                        "is_client_special" => false,
                        "created_at" => $startTime,
                        "updated_at" => $startTime,
                    ]);
                    broadcast(new ClientBookingTicket([[
                        "name" => $seat["name"],
                        "hall" => $seat["hall"],
                    ]], $eventId))->toOthers();
                } else {
                    $booking = BookModel::where([
                        ["seat_id", "=", data_get($seat, "id")],
                        ["event_id", "=", $eventId],
                        ["is_temporary", "=", true],
                        ["token", "=", $token],
                    ])->first();
                    if (!$booking) continue;
                    DiscountServiceUltils::releaseDiscountInUsed($booking);
                    $booking->delete();
                    broadcast(new ClientRemoveBookingTicket([[
                        "name" => $seat["name"],
                        "hall" => $seat["hall"],
                    ]], $ticketClass->event))->toOthers();
                }
            }
        }
        return $token;
    }

    public function getListTemporaryBookings(string $token, int $eventId)
    {
        $bookings = BookModel::with("seat")->where([
            "token" => $token,
            "event_id" => $eventId
        ])->get(["event_id", "seat_id", "discount_code"])->toArray();
        $discount = sizeof($bookings) ? DiscountModel::where("discount_code", data_get($bookings, "0.discount_code"))->first() : null;
        if ($discount) {
            $discount->applied = collect($bookings)->pluck("seat_id");
        }
        return [
            "bookings" => $bookings,
            "discount" => $discount
        ];
    }
}
