<?php

namespace App\Console\Commands;

use App\Models\BookModel;
use App\Models\EventSeatClassModel;
use App\Models\TicketClassModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AddPricingFromTicketToBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-pricing-from-ticket-to-booking';

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
        $bookings = BookModel::where("pricing", null)->get();
        $bookings->each(function (BookModel $booking) {
            $ticketClass = TicketClassModel::find($booking->ticket_class_id);
            $updateData = [];
            if ($ticketClass) {
                $updateData = [
                    "pricing" => $ticketClass->price
                ];
            } else {
                $eventSeatClass = EventSeatClassModel::with("ticketClass")->where("seat_id", $booking->seat_id)->where("event_id", $booking->event_id)->first();
                if ($eventSeatClass) {
                    $updateData = [
                        "pricing" => $eventSeatClass->ticketClass->price,
                        "ticket_class_id" => $eventSeatClass->ticket_class_id
                    ];
                }
            }
            Log::info("Update booking $booking->id:", $updateData);
            BookModel::find($booking->id)->update($updateData);
        });
    }
}
