<?php

namespace App\Services\Admin;

use App\Events\AdminSystemNotification;
use App\Exports\AggregateRevenueDaily;
use App\Models\BookModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\FileModel;
use App\Models\TicketClassModel;
use Carbon\Carbon;
use Exception;
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
        $exportType = data_get($data, "type");

        $fileName = "HGO_TICKET_" . time();
        try {
            $dataClientBookingsSpecial = [];
            $dataClientBookingsOnline = [];
            $eventsTicketsBooked = [];
            if ($exportType == "report-daily") {
                $endDate = Carbon::createFromFormat("Y-m-d H:i:s", "$endDate 00:00:00")->addDay(1)->setTimezone("-7")->format("Y-m-d H:i:s");
                $startDate = Carbon::createFromFormat("Y-m-d H:i:s", "$startDate 00:00:00")->setTimezone("-7")->format("Y-m-d H:i:s");
            }
            $bookings = BookModel::with(["client", "seat"])->where("start_pending", null);
            $bookings = $bookings->whereIn("event_id", data_get($data, "events"))->get();
            $events = EventModel::with(["ticketClasses"])->whereIn("id", $eventIds)->get();
            $bookings->each(function ($booking) use (&$dataClientBookingsSpecial, &$dataClientBookingsOnline, &$eventsTicketsBooked, $startDate, $endDate, $exportType) {
                $bookingEventId = $booking->event_id;
                $ticketClassId = $booking->ticket_class_id;
                $dataRef = &$dataClientBookingsOnline;
                if ($exportType == "report-daily" && !Carbon::parse($booking->created_at)->between(Carbon::parse($startDate), Carbon::parse($endDate))) goto NEXT;
                if ($booking->client->isSpecial) $dataRef = &$dataClientBookingsSpecial;
                foreach ($dataRef as &$data) {
                    if (
                        (!$booking->client->isSpecial && ($data["phone_number"] == $booking->client->phone_number || $data["id_number"] == $booking->client->id_number))
                        || ($booking->client->isSpecial && $data["id"] == $booking->client_id)
                    ) {
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
                $dataRef[$booking->client_id] = [
                    ...$booking->client->toArray(),
                    "events" => [
                        $bookingEventId => [
                            $ticketClassId => [$booking->seat->name]
                        ]
                    ]
                ];
                NEXT:
                if (!isset($eventsTicketsBooked[$bookingEventId])) $eventsTicketsBooked[$bookingEventId] = [];
                if (!isset($eventsTicketsBooked[$bookingEventId][$ticketClassId])) $eventsTicketsBooked[$bookingEventId][$ticketClassId] = [];
                $eventsTicketsBooked[$bookingEventId][$ticketClassId][] = $booking->seat->name;
            });

            usort($dataClientBookingsSpecial, fn ($clientA, $clientB) => strcasecmp($clientA["name"], $clientB["name"]));
            usort($dataClientBookingsOnline, fn ($clientA, $clientB) => strcasecmp($clientA["name"], $clientB["name"]));

            $this->aggregateRevenueDaily->export($dataClientBookingsOnline, $dataClientBookingsSpecial, $eventsTicketsBooked, $events, $fileName);
            AdminSystemNotification::dispatch("Xuất file báo cáo $fileName.xlsx thành công!", true);
            FileModel::create([
                "file_name" => $fileName,
                "is_exported" => true
            ]);
        } catch (Exception $e) {
            AdminSystemNotification::dispatch("Xuất file báo cáo $fileName.xlsx thất bại!", false);
            FileModel::create([
                "file_name" => $fileName,
                "is_failed" => true,
                "reason" => $e->getMessage()
            ]);
        }
    }
}
