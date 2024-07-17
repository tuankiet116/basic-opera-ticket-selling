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

        $fileName = "report_all_" . time() . "";
        if ($exportType == "report-daily") {
            $startDate = Carbon::createFromFormat("Y-m-d H:i:s", "$startDate 00:00:00")->setTimezone("-7")->format("Y-m-d H:i:s");
            $endDate = Carbon::createFromFormat("Y-m-d H:i:s", "$endDate 00:00:00")->addDay(1)->setTimezone("-7")->format("Y-m-d H:i:s");
            $fileName = "report_daily_" . time();
        }
        try {
            $dataClientBookingsSpecial = [];
            $dataClientBookingsOnline = [];
            $eventsTicketsBooked = [];
            $bookings = BookModel::with(["client", "seat"])->where("start_pending", null)
                ->where("is_temporary", 0);
            $bookings = $bookings->whereIn("event_id", data_get($data, "events"))->get();
            $events = EventModel::with(["ticketClasses", "discounts"])->whereIn("id", $eventIds)->get();
            $eventsSaveToFile = collect($events)->select(["id", "name"])->all();
            // dd($bookings->pluck("client"));
            $bookings->each(function ($booking) use (&$dataClientBookingsSpecial, &$dataClientBookingsOnline, &$eventsTicketsBooked, $startDate, $endDate, $exportType) {
                $bookingEventId = $booking->event_id;
                $ticketClassId = $booking->ticket_class_id;
                $discountCode = $booking->discount_code;
                $dataRef = &$dataClientBookingsOnline;
                if ($exportType == "report-daily" && !Carbon::parse($booking->created_at)->between(Carbon::parse($startDate), Carbon::parse($endDate))) goto NEXT;
                if ($booking->client->isSpecial) $dataRef = &$dataClientBookingsSpecial;

                foreach ($dataRef as &$data) {
                    if (
                        (!$booking->client->isSpecial && $data["phone_number"] == $booking->client->phone_number)
                        || ($booking->client->isSpecial && $data["id"] == $booking->client_id)
                    ) {
                        $currentDataBooking = data_get($data, "events.$bookingEventId.$ticketClassId.$discountCode", []);
                        $currentDataBooking[] = $booking;
                        data_set($data, "events.$bookingEventId.$ticketClassId.$discountCode", $currentDataBooking);
                        goto NEXT;
                    }
                }
                $dataRef[$booking->client_id] = [
                    ...$booking->client->toArray(),
                    "events" => [
                        $bookingEventId => [
                            $ticketClassId => [
                                $discountCode => [$booking]
                            ]
                        ]
                    ]
                ];
                NEXT:
                $currentTicketsBooked = data_get($eventsTicketsBooked, "$bookingEventId.$ticketClassId", []);
                $currentTicketsBooked[] = $booking;
                data_set($eventsTicketsBooked, "$bookingEventId.$ticketClassId.$discountCode", $currentTicketsBooked);
            });
            usort($dataClientBookingsSpecial, fn ($clientA, $clientB) => strcasecmp($clientA["name"], $clientB["name"]));
            usort($dataClientBookingsOnline, fn ($clientA, $clientB) => strcasecmp($clientA["name"], $clientB["name"]));

            $this->aggregateRevenueDaily->export($dataClientBookingsOnline, $dataClientBookingsSpecial, $eventsTicketsBooked, $events, $fileName);
            AdminSystemNotification::dispatch("Xuất file báo cáo $fileName.xlsx thành công!", true);
            FileModel::create([
                "file_name" => $fileName,
                "is_exported" => true,
                "events" => $eventsSaveToFile,
                "start_date" => $exportType == "report-daily" ? $startDate : null,
                "end_date" => $exportType == "report-daily" ? $endDate : null,
            ]);
        } catch (Exception $e) {
            AdminSystemNotification::dispatch("Xuất file báo cáo $fileName.xlsx thất bại!", false);
            FileModel::create([
                "file_name" => $fileName,
                "is_failed" => true,
                "reason" => $e->getMessage(),
                "events" => $eventsSaveToFile
            ]);
        }
    }
}
