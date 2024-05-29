<?php

namespace App\Services\Admin;

use App\Exports\AggregateRevenueDaily;
use App\Models\BookModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\TicketClassModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExportService
{
    public function __construct(protected AggregateRevenueDaily $aggregateRevenueDaily)
    {
    }

    public function exportReportAggregateRevenue(array $data)
    {
        $eventIds = data_get($data, "events");
        $startDate = data_get($data, "start_date");
        $endDate = data_get($data, "end_date");

        if ($startDate == $endDate) $endDate = Carbon::createFromFormat("Y-m-d H:i:s", "$endDate 00:00:00")->addDay(1)->setTimezone("-7")->format("Y-m-d H:i:s");
        $startDate = Carbon::createFromFormat("Y-m-d H:i:s", "$startDate 00:00:00")->setTimezone("-7")->format("Y-m-d H:i:s");

        $dataClientBookings = [];
        $bookings = BookModel::with(["client", "seat"])->where([
            ["created_at", ">=", $startDate],
            ["created_at", "<", $endDate],
            ["start_pending", null]
        ])->whereIn("event_id", data_get($data, "events"))->get();
        $events = EventModel::with(["ticketClasses"])->whereIn("id", $eventIds)->get();
        $bookings->each(function ($booking) use (&$dataClientBookings) {
            $bookingEventId = $booking->event_id;
            $bookingSeatId = $booking->seat_id;
            $ticketClassId = $this->getTicketClassOfSeat($bookingEventId, $bookingSeatId);
            foreach ($dataClientBookings as &$data) {
                if ($data["phone_number"] == $booking->client->phone_number || $data["id_number"] == $booking->client->id_number) {
                    if (isset($data["events"][$bookingEventId])) {
                        if (isset($data["events"][$bookingEventId][$ticketClassId])) {
                            $data["events"][$bookingEventId][$ticketClassId][] = $booking->seat->name;
                        } else {
                            $data["events"][$bookingEventId][$ticketClassId] = [$booking->seat->name];
                        }
                    } else {
                        $data["events"][$bookingEventId] = [
                            $ticketClassId => [$booking->seat->name]
                        ];
                    }
                    goto NEXT;
                }
            }
            $dataClientBookings[$booking->client_id] = [
                ...$booking->client->toArray(),
                "events" => [
                    $bookingEventId => [
                        $ticketClassId => [$booking->seat->name]
                    ]
                ]
            ];
            NEXT:
        });
        dd($dataClientBookings);
        $this->aggregateRevenueDaily->export($bookings);
    }

    private function getTicketClassOfSeat($eventId, $seatId)
    {
        return EventSeatClassModel::where("event_id", $eventId)->where("seat_id", $seatId)->first()->ticket_class_id;
    }
}
