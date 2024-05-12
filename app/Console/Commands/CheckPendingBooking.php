<?php

namespace App\Console\Commands;

use App\Events\ClientRemoveBookingTicket;
use App\Models\BookModel;
use App\Models\ClientModel;
use Carbon\Carbon;
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
        $timeToCheck = Carbon::now()->subMinutes(30)->format('Y-m-d H:i:s');
        $bookingOverTime = BookModel::with(["client", "seat"])->where("start_pending", "<", $timeToCheck)
            ->where("isPending", true)->get();
        if (sizeof($bookingOverTime)) {
            $clientIds = [];
            $bookingIds = [];
            $bookingInformation = [];
            $seatRemoved = $bookingOverTime->pluck("seat");
            $bookingOverTime->each(function ($booking) use (&$bookingInformation, &$clientIds, &$bookingIds) {
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
            BookModel::whereIn("id", $bookingIds)->delete();
            ClientModel::whereIn("id", $clientIds)->delete();
            ClientRemoveBookingTicket::dispatch($seatRemoved->toArray());
            Log::info("Delete bookings overtime of clients: ", array_keys($bookingInformation));
        }
    }
}
