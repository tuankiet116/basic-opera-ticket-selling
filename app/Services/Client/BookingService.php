<?php

namespace App\Services\Client;

use App\Events\AdminClientBookingTicket;
use App\Events\ClientBookingTicket;
use App\Mail\AskingPayment;
use App\Models\BookModel;
use App\Models\ClientModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\SeatModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    public function booking(array $data)
    {
        DB::beginTransaction();
        try {
            $client = ClientModel::create([
                "name" => data_get($data, "name"),
                "email" => data_get($data, "email"),
                "phone_number" => data_get($data, "phone_number"),
                "is_receive_in_opera" => data_get($data, "is_receive_in_opera"),
                "address" => data_get($data, "address"),
                "event_id" => data_get($data, "event_id"),
                "id_number" => data_get($data, "id_number"),
                "isSpecial" => false
            ]);
            $bookings = data_get($data, "bookings");
            $seatsBooking = [];
            $dataBookingSendMail = [];
            $dataClient = $client->toArray();
            $dataBookings = [];
            foreach ($bookings as $booking) {
                $seats = SeatModel::whereIn("name", data_get($booking, "seats"))->where("hall", data_get($booking, "hall"))->get();
                $dataBookingSendMail[$booking["hall"]] = [];
                if ($seats) $seats = $seats->toArray();
                else $seats = [];
                array_push($seatsBooking, ...$seats);
                foreach ($seats as $seat) {
                    $bookingCreated = BookModel::create([
                        "event_id" => data_get($data, "event_id"),
                        "client_id" => data_get($client, "id"),
                        "seat_id" => $seat["id"],
                        "isPending" => true,
                        "start_pending" => Carbon::now()->format('Y-m-d H:i:s')
                    ])->toArray();
                    $ticketClass = EventSeatClassModel::with("ticketClass")->where("seat_id", $seat["id"])->where("event_id", data_get($data, "event_id"))->first();
                    if (!$ticketClass || !$ticketClass->ticketClass) throw new Exception("No ticket class found while booking.");
                    $bookingCreated["seat"] = $seat;
                    $bookingCreated["ticket_class"] = $ticketClass->ticketClass->toArray();

                    $dataBookings[] = $bookingCreated;
                    array_push($dataBookingSendMail[$booking["hall"]], [
                        "class" => $ticketClass->ticketClass->name,
                        "seat" => $seat["name"],
                        "price" => $ticketClass->ticketClass->price,
                    ]);
                }
            }
            $event = EventModel::find($data["event_id"]);

            Mail::to($client)->queue(new AskingPayment($event, $client, $dataBookingSendMail));

            // Chunk data before dispatch to broadcasr to avoid memory leaks and large amounts of data
            foreach (array_chunk($seatsBooking, CHUNK_SIZE_BROADCAST) as $seatsBookingChunk) {
                ClientBookingTicket::dispatch($seatsBookingChunk, $event);
            }

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
            Log::error("Error while customers booking tickets: ", $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Booking Error: " . $e->getMessage(), $data);
            return __("messages.booking.error");
        } finally {
            DB::commit();
        }
        return true;
    }
}
