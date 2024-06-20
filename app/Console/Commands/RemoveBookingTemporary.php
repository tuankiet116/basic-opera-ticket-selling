<?php

namespace App\Console\Commands;

use App\Events\ClientRemoveBookingTicket;
use App\Models\BookModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveBookingTemporary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-booking-temporary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $timeToCheck = Carbon::now()->subMinutes(11)->format('Y-m-d H:i:s');
            $bookingOverTime = BookModel::with(["seat", "event"])->where("created_at", "<", $timeToCheck)->where("is_temporary", true)->get();
            if (sizeof($bookingOverTime)) {
                $bookingIds = [];
                $eventSeats = [];
                $bookingOverTime->each(function ($booking) use (&$bookingIds, &$eventSeats) {
                    if (isset($eventSeats[$booking->event->id])) {
                        $eventSeats[$booking->event_id]["seats"][] = $booking->seat;
                    } else {
                        $eventSeats[$booking->event_id]["event"] = $booking->event;
                        $eventSeats[$booking->event_id]["seats"] = [$booking->seat];
                    }
                    $bookingIds[] = $booking->id;
                });
                foreach ($eventSeats as $eventSeat) {
                    foreach (array_chunk($eventSeat["seats"], CHUNK_SIZE_BROADCAST) as $seats) {
                        ClientRemoveBookingTicket::dispatch($seats, $eventSeat["event"]);
                        Log::info("Release seats in temporary booking: ", $seats);
                    }
                }
                BookModel::whereIn("id", $bookingIds)->delete();
            }
        } catch (Exception $e) {
            Log::error("Error on release seats in temporary booking: ". $e->getMessage());
        }
    }
}
