<?php

namespace App\Console\Commands;

use App\Events\AdminRemoveBookingTicket;
use App\Events\ClientRemoveBookingTicket;
use App\Models\BookModel;
use App\Models\ClientModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-pending-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Pending Bookings is orver 15 minutes or not to remove them.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $timeToCheck = Carbon::now()->subMinutes(20)->format('Y-m-d H:i:s');
            $bookingOverTime = BookModel::with(["client", "seat", "event"])->where("start_pending", "<", $timeToCheck)
                ->where("isPending", true)->get();
            if (sizeof($bookingOverTime)) {
                $clientIds = [];
                $bookingIds = [];
                $bookingInformation = [];
                $eventSeats = [];
                $bookingOverTime->each(function ($booking) use (&$bookingInformation, &$clientIds, &$bookingIds, &$eventSeats) {
                    if (isset($eventSeats[$booking->event->id])) {
                        $eventSeats[$booking->event_id]["seats"][] = $booking->seat;
                    } else {
                        $eventSeats[$booking->event_id]["client"] = $booking->client->toArray();
                        $eventSeats[$booking->event_id]["event"] = $booking->event;
                        $eventSeats[$booking->event_id]["seats"] = [$booking->seat];
                    }
                    $clientEmail = data_get($booking, "client.email");
                    $hall =  data_get($booking, "seat.hall");
                    $seatName = data_get($booking, "seat.name");
                    $bookingIds[] = $booking->id;
                    if (isset($bookingInformation[$clientEmail])) {
                        if (isset($bookingInformation[$clientEmail][$hall])) array_push($bookingInformation[$clientEmail][$hall], $seatName);
                        else $bookingInformation[$clientEmail][$hall] = [$seatName];
                    } else {
                        $bookingInformation[$clientEmail] = [
                            $hall => [$seatName],
                        ];
                        $clientIds[] = data_get($booking, "client.id");
                    }
                });
                foreach ($eventSeats as $eventSeat) {
                    foreach (array_chunk($eventSeat["seats"], CHUNK_SIZE_BROADCAST) as $seats) {
                        ClientRemoveBookingTicket::dispatch($seats, $eventSeat["event"]);
                    }
                    AdminRemoveBookingTicket::dispatch($eventSeat["client"], $eventSeat["event"]);
                }
                BookModel::whereIn("id", $bookingIds)->delete();
                ClientModel::whereIn("id", $clientIds)->delete();
                Log::info("Delete bookings overtime of clients: ", array_keys($bookingInformation));
                Log::info("Delete clients id of booking overtime: ", $clientIds);
            }
        } catch (Exception $e) {
            Log::error("Error on check pending booking command: ". $e->getMessage());
        }
    }
}
